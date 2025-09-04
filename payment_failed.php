<?php
include 'db.php';

$payment_id = $_GET['payment_id'] ?? '';
$reason = $_GET['reason'] ?? 'unknown';

$payment_info = null;
if (!empty($payment_id)) {
    // Fetch payment details
    $query = "SELECT p.*, k.name as student_name, par.name as parent_name 
              FROM payments p 
              LEFT JOIN kids k ON p.child_id = k.id 
              LEFT JOIN parents par ON p.parent_id = par.id 
              WHERE p.payment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_info = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - KidMate</title>
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
                        <!-- Failure Icon -->
                        <div class="mb-4">
                            <div class="failure-icon">
                                <i class="ri-close-circle-line text-danger" style="font-size: 4rem;"></i>
                            </div>
                        </div>
                        
                        <h3 class="text-danger mb-3">
                            <i class="ri-error-warning-line me-2"></i>
                            Payment Failed
                        </h3>
                        
                        <div class="alert alert-danger">
                            <h5>Sorry, your payment could not be processed.</h5>
                            <p class="mb-0">Reason: <?php echo ucfirst(htmlspecialchars($reason)); ?></p>
                        </div>
                        
                        <?php if ($payment_info): ?>
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
                                            <strong>Amount:</strong><br>
                                            <span class="h5 text-primary">
                                                <?php echo htmlspecialchars($payment_info['currency']); ?> 
                                                <?php echo number_format($payment_info['amount'], 2); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="text-start">
                                        <strong>Description:</strong><br>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($payment_info['description']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Common Reasons and Solutions -->
                        <div class="alert alert-info">
                            <h6><i class="ri-information-line me-2"></i>Common Reasons for Payment Failure:</h6>
                            <ul class="text-start mb-0">
                                <li>Insufficient funds in your account</li>
                                <li>Incorrect card details or expired card</li>
                                <li>Network connectivity issues</li>
                                <li>Bank declined the transaction</li>
                                <li>Payment timeout</li>
                            </ul>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <a href="pay.php?link=<?php echo $payment_id; ?>" class="btn btn-primary">
                                <i class="ri-refresh-line me-2"></i>
                                Try Again
                            </a>
                            <a href="payschool.php?payment_id=<?php echo $payment_id; ?>" class="btn btn-outline-primary">
                                <i class="ri-bank-card-line me-2"></i>
                                Try Different Payment Method
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="ri-home-line me-2"></i>
                                Return to Home
                            </a>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="ri-customer-service-line me-1"></i>
                                Need help? Contact support at support@kidmate.com or call +233 XX XXX XXXX
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 