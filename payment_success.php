<?php
include 'db.php';
include 'email_service.php';

$payment_link = $_GET['link'] ?? '';
$payment_info = null;

if (!empty($payment_link)) {
    // Fetch payment details from existing payments table
    $query = "SELECT p.*, k.name as student_name, par.name as parent_name 
              FROM payments p 
              LEFT JOIN kids k ON p.child_id = k.id 
              LEFT JOIN parents par ON p.parent_id = par.id 
              WHERE p.payment_id = ? AND p.status = 'paid'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $payment_link);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_info = $result->fetch_assoc();
    $stmt->close();
    
    // Send payment confirmation email if payment info is found
    if ($payment_info) {
        // Get parent email from the database
        $parent_query = "SELECT u.email FROM users u 
                        INNER JOIN parents p ON u.id = p.user_id 
                        WHERE p.id = ?";
        $parent_stmt = $conn->prepare($parent_query);
        $parent_stmt->bind_param("i", $payment_info['parent_id']);
        $parent_stmt->execute();
        $parent_result = $parent_stmt->get_result();
        $parent_email = $parent_result->fetch_assoc();
        $parent_stmt->close();
        
        if ($parent_email && $parent_email['email']) {
            $emailService = new EmailService();
            $emailService->sendPaymentConfirmation(
                $parent_email['email'],
                $payment_info['parent_name'] ?? 'Parent',
                $payment_info['amount'],
                $payment_info['payment_id'],
                $payment_info['journey_date']
            );
            
            // Also send payment confirmation to daviddors12@gmail.com for monitoring
            $emailService->sendPaymentConfirmation(
                "daviddors12@gmail.com",
                "Admin",
                $payment_info['amount'],
                $payment_info['payment_id'],
                $payment_info['journey_date']
            );
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - KidMate</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/davido15/pozy-static@main/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <?php if ($payment_info): ?>
                            <!-- Success Animation -->
                            <div class="mb-4">
                                <div class="success-checkmark">
                                    <div class="check-icon">
                                        <span class="icon-line line-tip"></span>
                                        <span class="icon-line line-long"></span>
                                        <div class="icon-circle"></div>
                                        <div class="icon-fix"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3 class="text-success mb-3">
                                <i class="ri-check-line me-2"></i>
                                Payment Successful!
                            </h3>
                            
                            <div class="alert alert-success">
                                <h5>Thank you for your payment!</h5>
                                <p class="mb-0">Your payment has been processed successfully.</p>
                            </div>
                            
                            <!-- Payment Details -->
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">Payment Details</h6>
                                    <div class="row text-start">
                                        <div class="col-6">
                                            <strong>Student:</strong><br>
                                            <?php echo htmlspecialchars($payment_info['student_name']); ?>
                                        </div>
                                        <div class="col-6">
                                            <strong>Parent:</strong><br>
                                            <?php echo htmlspecialchars($payment_info['parent_name'] ?? 'N/A'); ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row text-start">
                                        <div class="col-6">
                                            <strong>Amount Paid:</strong><br>
                                            <span class="h5 text-success">
                                                <?php echo htmlspecialchars($payment_info['currency']); ?> 
                                                <?php echo number_format($payment_info['amount'], 2); ?>
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Payment Date:</strong><br>
                                            <?php echo date('M d, Y H:i', strtotime($payment_info['updated_at'])); ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row text-start">
                                        <div class="col-6">
                                            <strong>Payment Method:</strong><br>
                                            <?php echo ucfirst(str_replace('_', ' ', $payment_info['payment_method'])); ?>
                                        </div>
                                        <div class="col-6">
                                            <strong>Payment ID:</strong><br>
                                            <code><?php echo htmlspecialchars($payment_info['payment_id']); ?></code>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="text-start">
                                        <strong>Payment Reason:</strong><br>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($payment_info['description']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="window.print()">
                                    <i class="ri-printer-line me-2"></i>
                                    Print Receipt
                                </button>
                                <a href="index.php" class="btn btn-primary">
                                    <i class="ri-home-line me-2"></i>
                                    Return to Home
                                </a>
                            </div>
                            
                            <div class="mt-4">
                                <small class="text-muted">
                                    <i class="ri-mail-line me-1"></i>
                                    A confirmation email has been sent to your registered email address.
                                </small>
                            </div>
                            
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="ri-error-warning-line me-2"></i>
                                Payment information not found or invalid payment link.
                            </div>
                            <a href="index.php" class="btn btn-primary">Return to Home</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .success-checkmark {
        width: 80px;
        height: 80px;
        margin: 0 auto;
    }
    
    .check-icon {
        width: 80px;
        height: 80px;
        position: relative;
        border-radius: 50%;
        box-sizing: content-box;
        border: 4px solid #4CAF50;
    }
    
    .check-icon::before {
        top: 3px;
        left: -2px;
        width: 30px;
        transform-origin: 100% 50%;
        border-radius: 100px 0 0 100px;
    }
    
    .check-icon::after {
        top: 0;
        left: 30px;
        width: 60px;
        transform-origin: 0 50%;
        border-radius: 0 100px 100px 0;
        animation: rotate-circle 4.25s ease-in;
    }
    
    .check-icon::before, .check-icon::after {
        content: '';
        height: 100px;
        position: absolute;
        background: #FFFFFF;
        transform: rotate(-45deg);
    }
    
    .check-icon .icon-line {
        height: 5px;
        background-color: #4CAF50;
        display: block;
        border-radius: 2px;
        position: absolute;
        z-index: 10;
    }
    
    .check-icon .icon-line.line-tip {
        top: 46px;
        left: 14px;
        width: 25px;
        transform: rotate(45deg);
        animation: icon-line-tip 0.75s;
    }
    
    .check-icon .icon-line.line-long {
        top: 38px;
        right: 8px;
        width: 47px;
        transform: rotate(-45deg);
        animation: icon-line-long 0.75s;
    }
    
    .check-icon .icon-circle {
        top: -4px;
        left: -4px;
        z-index: 10;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        position: absolute;
        box-sizing: content-box;
        border: 4px solid rgba(76, 175, 80, .5);
    }
    
    .check-icon .icon-fix {
        top: 8px;
        width: 5px;
        left: 26px;
        z-index: 1;
        height: 85px;
        position: absolute;
        transform: rotate(-45deg);
        background-color: #FFFFFF;
    }
    
    @keyframes rotate-circle {
        0% {
            transform: rotate(-45deg);
        }
        5% {
            transform: rotate(-45deg);
        }
        12% {
            transform: rotate(-405deg);
        }
        100% {
            transform: rotate(-405deg);
        }
    }
    
    @keyframes icon-line-tip {
        0% {
            width: 0;
            left: 1px;
            top: 19px;
        }
        54% {
            width: 0;
            left: 1px;
            top: 19px;
        }
        70% {
            width: 65px;
            left: -8px;
            top: 37px;
        }
        84% {
            width: 17px;
            left: 21px;
            top: 48px;
        }
        100% {
            width: 25px;
            left: 14px;
            top: 46px;
        }
    }
    
    @keyframes icon-line-long {
        0% {
            width: 0;
            right: 46px;
            top: 54px;
        }
        65% {
            width: 0;
            right: 46px;
            top: 54px;
        }
        84% {
            width: 55px;
            right: 0px;
            top: 35px;
        }
        100% {
            width: 47px;
            right: 8px;
            top: 38px;
        }
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 