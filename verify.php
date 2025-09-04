<?php
include 'db.php';

// Include PHPMailer classes
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if admin is logged in (you can remove this if you want public access)
// if (!isset($_SESSION["email"])) {
//     header("Location: login.php");
//     exit();
// }

$pickup_id = $_GET['pickup_id'] ?? '';
$error_message = '';
$success_message = '';
$journey_data = null;
$otp_sent = false;
$otp_verified = false;
$email_sent_to = '';

// Handle OTP generation and sending
if (isset($_POST['action']) && $_POST['action'] == 'send_otp' && !empty($pickup_id)) {
    // Get journey information including dropoff coordinates
    $query = "SELECT pj.*, k.name as child_name, k.image as child_image, 
                     pp.name as pickup_person_name, pp.image as pickup_person_image,
                     u.email as parent_email, u.name as parent_name,
                     pj.dropoff_latitude, pj.dropoff_longitude, pj.dropoff_location
               FROM pickup_journey pj
               LEFT JOIN kids k ON pj.child_id = k.id
               LEFT JOIN pickup_persons pp ON pj.pickup_person_id = pp.uuid
               LEFT JOIN users u ON pj.parent_id = u.id
               WHERE pj.pickup_id = ?
               ORDER BY pj.timestamp DESC
               LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $pickup_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $journey_data = $result->fetch_assoc();
        $admin_email = 'daviddors12@gmail.com'; // Admin email for OTP verification
        
        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(0, 999999));
        
        // Set expiration time (10 minutes from now)
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Store OTP in database
        $otp_query = "INSERT INTO otp_codes (pickup_id, otp_code, email, expires_at) VALUES (?, ?, ?, ?)";
        $otp_stmt = $conn->prepare($otp_query);
        $otp_stmt->bind_param("ssss", $pickup_id, $otp, $admin_email, $expires_at);
            
            if ($otp_stmt->execute()) {
                // Send email with OTP to admin
                $to = $admin_email;
                $subject = "KidMate - Admin Journey Verification OTP";
                $message = "
                <html>
                <head>
                    <title>KidMate Admin Journey Verification</title>
                </head>
                <body>
                    <h2>KidMate Admin Journey Verification</h2>
                    <p>Hello Admin,</p>
                    <p>You have requested to verify the pickup journey for <strong>{$journey_data['child_name']}</strong>.</p>
                    <p>Journey Details:</p>
                    <ul>
                        <li><strong>Pickup ID:</strong> {$pickup_id}</li>
                        <li><strong>Child:</strong> {$journey_data['child_name']}</li>
                        <li><strong>Parent:</strong> {$journey_data['parent_name']}</li>
                        <li><strong>Pickup Person:</strong> {$journey_data['pickup_person_name']}</li>
                    </ul>
                    <p>Your admin verification code is: <strong style='font-size: 24px; color: #007bff;'>{$otp}</strong></p>
                    <p>This code will expire in 10 minutes.</p>
                    <p>If you didn't request this verification, please ignore this email.</p>
                    <br>
                    <p>Best regards,<br>KidMate Admin Team</p>
                </body>
                </html>
                ";
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: KidMate <noreply@kidmate.com>" . "\r\n";
                
                // Use PHPMailer for reliable email delivery
                try {
                    $mail = new PHPMailer(true);
                    
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.hostinger.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'schoolapp@outrankconsult.com';
                    $mail->Password   = 'Gq]PxrqB#sC2';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    
                    // Recipients
                    $mail->setFrom('schoolapp@outrankconsult.com', 'KidMate');
                    $mail->addAddress($admin_email, 'Admin');
                    
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;
                    $mail->AltBody = "Your KidMate admin verification code is: $otp\n\nThis code will expire in 10 minutes.\n\nJourney: $pickup_id\nChild: {$journey_data['child_name']}";
                    
                    // Send the email
                    $mail->send();
                    
                    $otp_sent = true;
                    $email_sent_to = $admin_email;
                    $success_message = "OTP has been sent to the admin email address ($admin_email).";
                    error_log("KidMate OTP Email SUCCESS - To: $admin_email, OTP: $otp");
                    
                } catch (Exception $e) {
                    $error_message = "Failed to send OTP email: " . $e->getMessage();
                    error_log("KidMate OTP Email FAILED - To: $admin_email, Error: " . $e->getMessage());
                }
            } else {
                $error_message = "Failed to generate OTP. Please try again.";
            }
    } else {
        $error_message = "No journey found with the provided pickup ID.";
    }
}

// Handle OTP verification
if (isset($_POST['action']) && $_POST['action'] == 'verify_otp') {
    $entered_otp = $_POST['otp'] ?? '';
    
    // Verify OTP from database
    $verify_query = "SELECT * FROM otp_codes 
                     WHERE pickup_id = ? AND otp_code = ? AND is_used = 0 AND expires_at > NOW()
                     ORDER BY created_at DESC LIMIT 1";
    
    $verify_stmt = $conn->prepare($verify_query);
    $verify_stmt->bind_param("ss", $pickup_id, $entered_otp);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows > 0) {
        $otp_data = $verify_result->fetch_assoc();
        
        // Mark OTP as used
        $update_query = "UPDATE otp_codes SET is_used = 1, used_at = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $otp_data['id']);
        $update_stmt->execute();
        
        // Get journey data including dropoff coordinates
        $query = "SELECT pj.*, k.name as child_name, k.image as child_image, 
                         pp.name as pickup_person_name, pp.image as pickup_person_image,
                         u.email as parent_email, u.name as parent_name, u.phone as parent_phone,
                         pj.dropoff_latitude, pj.dropoff_longitude, pj.dropoff_location
                  FROM pickup_journey pj
                  LEFT JOIN kids k ON pj.child_id = k.id
                  LEFT JOIN pickup_persons pp ON pj.pickup_person_id = pp.uuid
                  LEFT JOIN users u ON pj.parent_id = u.id
                  WHERE pj.pickup_id = ?
                  ORDER BY pj.timestamp DESC
                  LIMIT 1";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $pickup_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $journey_data = $result->fetch_assoc();
            $otp_verified = true;
            $success_message = "OTP verified successfully!";
        }
    } else {
        $error_message = "Invalid or expired OTP. Please try again.";
    }
}

// Handle picked action (changed from depart)
if (isset($_POST['action']) && $_POST['action'] == 'picked' && !empty($pickup_id)) {
    // Insert a new journey record with 'picked' status, preserving all existing data
    $query = "INSERT INTO pickup_journey (pickup_id, parent_id, child_id, pickup_person_id, status, timestamp, dropoff_location, dropoff_latitude, dropoff_longitude) 
              SELECT pickup_id, parent_id, child_id, pickup_person_id, 'picked', NOW(), dropoff_location, dropoff_latitude, dropoff_longitude
              FROM pickup_journey 
              WHERE pickup_id = ? 
              ORDER BY timestamp DESC 
              LIMIT 1";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $pickup_id);
    
    if ($stmt->execute()) {
        $success_message = "Child picked up successfully!";
        
        // Refresh journey data to show updated status including dropoff coordinates
        $query = "SELECT pj.*, k.name as child_name, k.image as child_image, 
                         pp.name as pickup_person_name, pp.image as pickup_person_image,
                         u.email as parent_email, u.name as parent_name, u.phone as parent_phone,
                         pj.dropoff_latitude, pj.dropoff_longitude, pj.dropoff_location
                  FROM pickup_journey pj
                  LEFT JOIN kids k ON pj.child_id = k.id
                  LEFT JOIN pickup_persons pp ON pj.pickup_person_id = pp.uuid
                  LEFT JOIN users u ON pj.parent_id = u.id
                  WHERE pj.pickup_id = ?
                  ORDER BY pj.timestamp DESC
                  LIMIT 1";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $pickup_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $journey_data = $result->fetch_assoc();
        }
    } else {
        $error_message = "Failed to record pickup. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Journey - KidMate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/davido15/pozy-static@main/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        .main-content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .content {
            padding: 20px;
        }
        .verify-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .journey-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            color: #333;
        }
        .info-value {
            color: #666;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-picked { background: #d1ecf1; color: #0c5460; }
        .status-dropoff { background: #d4edda; color: #155724; }
        .status-completed { background: #cce5ff; color: #004085; }
        .status-departed { background: #f8d7da; color: #721c24; }
        .otp-input {
            font-size: 24px;
            text-align: center;
            letter-spacing: 5px;
            width: 200px;
        }
        .child-image, .picker-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }
        .journey-timeline {
            margin-top: 15px;
        }
        .timeline-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            margin: 5px 0;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .timeline-status {
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        .timeline-time {
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background: #e0a800;
        }
        .btn-outline-primary {
            background: transparent;
            color: #007bff;
            border: 1px solid #007bff;
        }
        .btn-outline-primary:hover {
            background: #007bff;
            color: white;
        }
        .btn-outline-secondary {
            background: transparent;
            color: #6c757d;
            border: 1px solid #6c757d;
        }
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeaa7;
        }
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            margin-bottom: 15px;
            color: #333;
        }
        .card-text {
            color: #666;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
        }
        @media (max-width: 768px) {
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="content">
            <div class="verify-container">
                <h2><i class="ri-shield-check-line"></i> Journey Verification</h2>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (empty($pickup_id)): ?>
                    <div class="alert alert-warning">
                        <h4>No Pickup ID Provided</h4>
                        <p>Please scan a QR code or use a valid link to access journey verification.</p>
                    </div>
                <?php elseif (!$otp_sent && !$journey_data): ?>
                    <!-- Initial form to send OTP -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Verify Journey: <?php echo htmlspecialchars($pickup_id); ?></h5>
                            <p class="card-text">To view the journey information, we need to send a verification code to the registered email address.</p>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="send_otp">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-mail-send-line"></i> Send Verification Code
                                </button>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($otp_sent && !$otp_verified): ?>
                    <!-- OTP verification form -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Enter Verification Code</h5>
                            <p class="card-text">A verification code has been sent to: <strong><?php echo htmlspecialchars($email_sent_to); ?></strong></p>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="verify_otp">
                                <div class="form-group">
                                    <label for="otp">Enter 6-digit code:</label>
                                    <input type="text" id="otp" name="otp" class="form-control otp-input" 
                                           maxlength="6" pattern="[0-9]{6}" required 
                                           placeholder="000000">
                                </div>
                                <button type="submit" class="btn btn-success mt-3">
                                    <i class="ri-check-line"></i> Verify Code
                                </button>
                            </form>
                            
                            <form method="POST" class="mt-3">
                                <input type="hidden" name="action" value="send_otp">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="ri-refresh-line"></i> Resend Code
                                </button>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($otp_verified && $journey_data): ?>
                    <!-- Display journey information only after OTP verification -->
                    <div class="journey-info">
                        <h4><i class="ri-route-line"></i> Journey Information</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="ri-user-heart-line"></i> Child Information</h5>
                                <div class="info-row">
                                    <span class="info-label">Name:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($journey_data['child_name']); ?></span>
                                </div>
                                <?php if (!empty($journey_data['child_image'])): ?>
                                    <div class="text-center mb-3">
                                        <img src="<?php echo htmlspecialchars($journey_data['child_image']); ?>" 
                                             alt="Child" class="child-image">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6">
                                <h5><i class="ri-user-star-line"></i> Pickup Person</h5>
                                <div class="info-row">
                                    <span class="info-label">Name:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($journey_data['pickup_person_name']); ?></span>
                                </div>
                                <?php if (!empty($journey_data['pickup_person_image'])): ?>
                                    <div class="text-center mb-3">
                                        <img src="<?php echo htmlspecialchars($journey_data['pickup_person_image']); ?>" 
                                             alt="Pickup Person" class="picker-image">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5><i class="ri-home-heart-line"></i> Parent Information</h5>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value"><?php echo htmlspecialchars($journey_data['parent_name']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($journey_data['parent_email']); ?></span>
                        </div>
                        <?php if (!empty($journey_data['parent_phone'])): ?>
                            <div class="info-row">
                                <span class="info-label">Phone:</span>
                                <span class="info-value"><?php echo htmlspecialchars($journey_data['parent_phone']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <h5><i class="ri-time-line"></i> Journey Details</h5>
                        <div class="info-row">
                            <span class="info-label">Pickup ID:</span>
                            <span class="info-value"><?php echo htmlspecialchars($journey_data['pickup_id']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge status-<?php echo $journey_data['status']; ?>">
                                    <?php echo ucfirst($journey_data['status']); ?>
                                </span>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Timestamp:</span>
                            <span class="info-value"><?php echo date('F j, Y g:i A', strtotime($journey_data['timestamp'])); ?></span>
                        </div>
                    </div>
                    
                    <!-- Journey Timeline -->
                    <h5><i class="ri-time-line"></i> Journey Timeline</h5>
                    <div class="journey-timeline">
                        <?php
                        $timeline_query = "SELECT status, timestamp FROM pickup_journey 
                                          WHERE pickup_id = ? 
                                          ORDER BY timestamp ASC";
                        $stmt = $conn->prepare($timeline_query);
                        $stmt->bind_param("s", $pickup_id);
                        $stmt->execute();
                        $timeline_result = $stmt->get_result();
                        
                        while ($timeline_row = $timeline_result->fetch_assoc()):
                        ?>
                            <div class="timeline-item">
                                <div class="timeline-status status-<?php echo $timeline_row['status']; ?>">
                                    <?php echo ucfirst($timeline_row['status']); ?>
                                </div>
                                <div class="timeline-time">
                                    <?php echo date('g:i A', strtotime($timeline_row['timestamp'])); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="text-center mt-4">
                        <?php if ($journey_data['status'] != 'picked' && $journey_data['status'] != 'completed'): ?>
                            <form method="POST" style="display: inline-block; margin-right: 10px;">
                                <input type="hidden" name="action" value="picked">
                                <button type="submit" class="btn btn-success" 
                                        onclick="return confirm('Are you sure the child has been picked up?')">
                                    <i class="ri-user-heart-line"></i> Mark as Picked Up
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <a href="verify.php" class="btn btn-outline-primary">
                            <i class="ri-refresh-line"></i> Verify Another Journey
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Auto-focus on OTP input
        $(document).ready(function() {
            $('#otp').focus();
            
            // Auto-format OTP input
            $('#otp').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 6) {
                    $(this).closest('form').submit();
                }
            });
        });
    </script>
</body>
</html> 