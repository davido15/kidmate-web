<?php
include 'db.php';

$payment_link = $_GET['link'] ?? '';
$error = '';
$payment_info = null;

if (empty($payment_link)) {
    $error = "Invalid payment link.";
} else {
    // Fetch payment details from existing payments table
    $query = "SELECT p.*, 
                     CASE 
                         WHEN par.name IS NOT NULL THEN par.name 
                         ELSE CONCAT('Parent ID: ', p.parent_id) 
                     END as parent_name,
                     par.phone as parent_phone 
              FROM payments p 
              LEFT JOIN parents par ON CAST(p.parent_id AS UNSIGNED) = par.id 
              WHERE p.payment_id = ? AND p.status = 'pending'";
    
    // Debug: Print the query and parameters
    echo "<!-- Debug: Query: " . $query . " -->\n";
    echo "<!-- Debug: Payment Link: " . $payment_link . " -->\n";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $payment_link);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_info = $result->fetch_assoc();
    
    // Debug: Print the result
    echo "<!-- Debug: Payment Info: " . print_r($payment_info, true) . " -->\n";
    
    if (!$payment_info) {
        $error = "Payment link not found or already processed.";
    }
    $stmt->close();
}

// Handle payment submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && $payment_info) {
    $payment_method = $_POST['payment_method'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $reference = $_POST['reference'] ?? '';
    
    if (!empty($payment_method)) {
        // Generate payment reference if not provided
        if (empty($reference)) {
            // Generate a more robust reference ID
            $timestamp = time();
            $random = mt_rand(100000, 999999);
            $student_id = $payment_info['child_id'] ?? '000';
            $reference = 'KIDMATE_' . $timestamp . '_' . $student_id . '_' . $random;
        }
        
        // Handle payment gateway integration directly
        $email = $_POST['email'] ?? '';
        
        if (!empty($email)) {
            // Initialize payment with third-party gateway (e.g., Paystack)
            $gateway_url = "https://api.paystack.co/transaction/initialize";
            $gateway_data = [
                'amount' => $payment_info['amount'] * 100, // Convert to smallest currency unit
                'email' => $email,
                'reference' => $reference,
                'callback_url' => "https://outrankconsult.com/payment/KidMate/paycallback.php",
                'metadata' => [
                    'payment_id' => $payment_link,
                    'child_id' => $payment_info['child_id'],
                    'parent_name' => $payment_info['parent_name'] ?? 'Unknown',
                    'description' => $payment_info['description'],
                    'payment_method' => $payment_method,
                    'phone_number' => $phone_number
                ]
            ];
            
            // Make API call to payment gateway
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $gateway_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gateway_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer sk_test_d7543a492364e058e159881177924ffa48872bc3",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            $response_data = json_decode($response, true);
            
            if ($response_data['status'] === true) {
                // Redirect to payment gateway
                $authorization_url = $response_data['data']['authorization_url'];
                header("Location: " . $authorization_url);
                exit;
            } else {
                // Payment initialization failed
                $error = "Payment initialization failed: " . ($response_data['message'] ?? 'Unknown error');
            }
        } else {
            $error = "Email address is required for payment processing.";
        }
    } else {
        $error = "Please select a payment method.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KidMate Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/davido15/pozy-static@main/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">
                            <i class="ri-money-dollar-circle-line me-2"></i>
                            KidMate Payment Portal
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="ri-error-warning-line me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                            <div class="text-center">
                                <a href="index.php" class="btn btn-primary">Return to Home</a>
                            </div>
                        <?php elseif ($payment_info): ?>
                            <!-- Payment Information -->
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="ri-bank-card-line text-primary" style="font-size: 3rem;"></i>
                                </div>
                                <h5>Payment Details</h5>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-6">
                                    <strong>Child ID:</strong><br>
                                    <?php echo htmlspecialchars($payment_info['child_id']); ?>
                                </div>
                                <div class="col-6">
                                    <strong>Parent:</strong><br>
                                    <?php echo htmlspecialchars($payment_info['parent_name'] ?? 'N/A'); ?>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-6">
                                    <strong>Amount:</strong><br>
                                    <span class="h4 text-primary">
                                        <?php echo htmlspecialchars($payment_info['currency']); ?> 
                                        <?php echo number_format($payment_info['amount'], 2); ?>
                                    </span>
                                </div>
                                <div class="col-6">
                                    <strong>Due Date:</strong><br>
                                    <?php echo $payment_info['journey_date'] ? date('M d, Y', strtotime($payment_info['journey_date'])) : 'Not specified'; ?>
                                </div>
                            </div>
                            
                                                            <div class="mb-4">
                                    <strong>Payment Reason:</strong><br>
                                    <p class="text-muted"><?php echo htmlspecialchars($payment_info['description']); ?></p>
                                </div>
                            
                            <hr>
                            
                            <!-- Payment Form -->
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="">-- Select Payment Method --</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="card">Credit/Debit Card</option>
                                        <option value="cash">Cash Payment</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Phone Number (for Mobile Money)</label>
                                    <input type="tel" name="phone_number" class="form-control" 
                                           placeholder="Enter phone number" 
                                           value="<?php echo htmlspecialchars($payment_info['parent_phone'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required 
                                           placeholder="Enter your email address">
                                    <small class="form-text text-muted">Payment receipt will be sent to this email</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Payment Reference (Optional)</label>
                                    <input type="text" name="reference" class="form-control" 
                                           placeholder="Enter payment reference">
                                    <small class="form-text text-muted">Leave blank to auto-generate</small>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="ri-lock-line me-2"></i>
                                        Pay Now - <?php echo htmlspecialchars($payment_info['currency']); ?> 
                                        <?php echo number_format($payment_info['amount'], 2); ?>
                                    </button>
                                </div>
                            </form>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="ri-shield-check-line me-1"></i>
                                    Secure payment powered by KidMate
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <h6>Need Help?</h6>
                        <p class="mb-1">
                            <i class="ri-phone-line me-2"></i>
                            Contact: +233 XX XXX XXXX
                        </p>
                        <p class="mb-0">
                            <i class="ri-mail-line me-2"></i>
                            Email: support@kidmate.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 