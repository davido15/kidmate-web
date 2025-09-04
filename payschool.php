<?php
session_start();
include 'db.php';

$payment_id = $_GET['payment_id'] ?? '';
$error = '';
$payment_data = $_SESSION['payment_data'] ?? null;

// Validate payment data
if (empty($payment_id) || !$payment_data || $payment_data['payment_id'] !== $payment_id) {
    $error = "Invalid payment session. Please try again.";
} else {
    // Verify payment is still pending
    $query = "SELECT p.*, k.name as student_name, par.name as parent_name 
              FROM payments p 
              LEFT JOIN kids k ON p.child_id = k.id 
              LEFT JOIN parents par ON p.parent_id = par.id 
              WHERE p.payment_id = ? AND p.status = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_info = $result->fetch_assoc();
    $stmt->close();
    
    if (!$payment_info) {
        $error = "Payment not found or already processed.";
    }
}

// Handle payment gateway submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$error) {
    $amount = $payment_data['amount'];
    $currency = $payment_data['currency'];
    $reference = $payment_data['reference'];
    $email = $_POST['email'] ?? '';
    
    if (!empty($email)) {
        // Initialize payment with third-party gateway (e.g., Paystack)
        $gateway_url = "https://api.paystack.co/transaction/initialize";
        $gateway_data = [
            'amount' => $amount * 100, // Convert to smallest currency unit
            'email' => $email,
            'reference' => $reference,
            'callback_url' => "https://outrankconsult.com/payment/KidMate/paycallback.php",
            'metadata' => [
                'payment_id' => $payment_id,
                'student_name' => $payment_data['student_name'],
                'description' => $payment_data['description']
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
            // Get the third-party reference
            $gateway_reference = $response_data['data']['reference'];
            
            // Update database with the third-party reference
            $update_query = "UPDATE payments SET 
                            updated_at = NOW()
                            WHERE payment_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("s", $payment_id);
            $stmt->execute();
            $stmt->close();
            
            // Redirect to payment gateway
            $authorization_url = $response_data['data']['authorization_url'];
            header("Location: " . $authorization_url);
            exit;
        } else {
            // Payment initialization failed
            $error = "Payment initialization failed: " . ($response_data['message'] ?? 'Unknown error');
        }
    } else {
        $error = "Email address is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway - KidMate</title>
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
                            <i class="ri-bank-card-line me-2"></i>
                            Payment Gateway
                        </h4>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="ri-error-warning-line me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                            <div class="text-center">
                                <a href="pay.php?link=<?php echo $payment_id; ?>" class="btn btn-primary">Try Again</a>
                            </div>
                        <?php elseif ($payment_data): ?>
                            <!-- Payment Summary -->
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="ri-shield-check-line text-success" style="font-size: 3rem;"></i>
                                </div>
                                <h5>Payment Summary</h5>
                            </div>
                            
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong>Student:</strong><br>
                                            <?php echo htmlspecialchars($payment_data['student_name']); ?>
                                        </div>
                                        <div class="col-6">
                                            <strong>Amount:</strong><br>
                                            <span class="h5 text-primary">
                                                <?php echo htmlspecialchars($payment_data['currency']); ?> 
                                                <?php echo number_format($payment_data['amount'], 2); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong>Reference:</strong><br>
                                            <code><?php echo htmlspecialchars($payment_data['reference']); ?></code>
                                        </div>
                                        <div class="col-6">
                                            <strong>Due Date:</strong><br>
                                            <?php echo isset($payment_data['due_date']) && $payment_data['due_date'] ? date('M d, Y', strtotime($payment_data['due_date'])) : 'Not specified'; ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Description:</strong><br>
                                        <p class="text-muted mb-0"><?php echo htmlspecialchars($payment_data['description']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Form -->
                            <form method="POST" id="payment-form">
                                <div class="mb-3">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required 
                                           placeholder="Enter your email address">
                                    <small class="form-text text-muted">Payment receipt will be sent to this email</small>
                                </div>
                                
                                <div class="alert alert-info">
                                    <h6><i class="ri-information-line me-2"></i>Payment Process:</h6>
                                    <ol class="mb-0">
                                        <li>Enter your email address</li>
                                        <li>You'll be redirected to our secure payment processor</li>
                                        <li>Choose your preferred payment method</li>
                                        <li>Complete your payment securely</li>
                                    </ol>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="ri-lock-line me-2"></i>
                                        Proceed to Payment
                                    </button>
                                    <a href="pay.php?link=<?php echo $payment_id; ?>" class="btn btn-outline-secondary">
                                        <i class="ri-arrow-left-line me-2"></i>
                                        Back to Payment Details
                                    </a>
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
