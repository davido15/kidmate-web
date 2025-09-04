<?php
include 'db.php';

// Get callback parameters from Paystack
$status = $_GET['status'] ?? $_POST['status'] ?? '';
$reference = $_GET['reference'] ?? $_GET['trxref'] ?? $_POST['reference'] ?? $_POST['trxref'] ?? '';
$payment_id = $_GET['payment_id'] ?? $_POST['payment_id'] ?? '';

// For Paystack callback, if we have a reference but no status, assume success
if (!empty($reference) && empty($status)) {
    $status = 'success'; // Paystack typically sends reference only on successful payments
}

// For Paystack webhook, try to get payment_id from metadata
if (empty($payment_id) && !empty($reference)) {
    // Try to find payment by reference - check if reference matches payment_id
    $find_query = "SELECT payment_id FROM payments WHERE payment_id = ?";
    $stmt = $conn->prepare($find_query);
    $stmt->bind_param("s", $reference);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $payment_id = $row['payment_id'];
    } else {
        // If not found by exact match, try to find by partial reference in description
        $find_query2 = "SELECT payment_id FROM payments WHERE description LIKE ? OR payment_id LIKE ?";
        $stmt2 = $conn->prepare($find_query2);
        $search_term = '%' . $reference . '%';
        $stmt2->bind_param("ss", $search_term, $search_term);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        if ($row2 = $result2->fetch_assoc()) {
            $payment_id = $row2['payment_id'];
        }
        $stmt2->close();
    }
    $stmt->close();
}

// Log the callback for debugging
$log_message = date('Y-m-d H:i:s') . " - Callback received: Status=$status, Reference=$reference, Payment_ID=$payment_id\n";
file_put_contents('payment_callback.log', $log_message, FILE_APPEND);

// Handle successful payment
if ($status === 'success' || $status === 'completed') {
    // Update payment status to paid in database
    $update_query = "UPDATE payments SET status = 'paid', updated_at = NOW() WHERE payment_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("s", $payment_id);
    
    if ($stmt->execute()) {
        // Log successful update
        file_put_contents('payment_callback.log', date('Y-m-d H:i:s') . " - Payment status updated to PAID for payment_id: $payment_id\n", FILE_APPEND);
        $success = true;
        $message = "Payment completed successfully!";
    } else {
        // Log error
        file_put_contents('payment_callback.log', date('Y-m-d H:i:s') . " - Error updating payment: " . $stmt->error . "\n", FILE_APPEND);
        $success = false;
        $message = "Error updating payment status.";
    }
    $stmt->close();
    
} elseif ($status === 'failed' || $status === 'cancelled') {
    // Update payment status to failed in database
    $update_query = "UPDATE payments SET status = 'failed', updated_at = NOW() WHERE payment_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("s", $payment_id);
    $stmt->execute();
    $stmt->close();
    
    // Log failed payment
    file_put_contents('payment_callback.log', date('Y-m-d H:i:s') . " - Payment status updated to FAILED for payment_id: $payment_id\n", FILE_APPEND);
    $success = false;
    $message = "Payment was cancelled or failed.";
    
} else {
    // Unknown status
    file_put_contents('payment_callback.log', date('Y-m-d H:i:s') . " - Unknown payment status: $status for payment_id: $payment_id\n", FILE_APPEND);
    $success = false;
    $message = "Unknown payment status received.";
}

// Get payment details for display
$payment_info = null;
if (!empty($payment_id)) {
    $query = "SELECT p.*, par.name as parent_name 
              FROM payments p 
              LEFT JOIN parents par ON CAST(p.parent_id AS UNSIGNED) = par.id 
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
    <title>Payment Result - KidMate</title>
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
                            Payment Result
                        </h4>
                    </div>
                    
                    <div class="card-body text-center">
                        <?php if ($success): ?>
                            <!-- Success Display -->
                            <div class="mb-4">
                                <i class="ri-check-circle-line text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-success mb-3">Payment Successful!</h3>
                            <div class="alert alert-success">
                                <h5><?php echo htmlspecialchars($message); ?></h5>
                            </div>
                        <?php else: ?>
                            <!-- Error Display -->
                            <div class="mb-4">
                                <i class="ri-error-warning-line text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-danger mb-3">Payment Issue</h3>
                            <div class="alert alert-danger">
                                <h5><?php echo htmlspecialchars($message); ?></h5>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($payment_info): ?>
                            <!-- Payment Details -->
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">Payment Details</h6>
                                    <div class="row text-start">
                                        <div class="col-6">
                                            <strong>Student:</strong><br>
                                            <?php echo htmlspecialchars($payment_info['student_name'] ?? 'N/A'); ?>
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
                                    <div class="row text-start">
                                        <div class="col-6">
                                            <strong>Payment ID:</strong><br>
                                            <code><?php echo htmlspecialchars($payment_info['payment_id']); ?></code>
                                        </div>
                                        <div class="col-6">
                                            <strong>Status:</strong><br>
                                            <span class="badge bg-<?php echo $payment_info['status'] == 'paid' ? 'success' : ($payment_info['status'] == 'failed' ? 'danger' : 'warning'); ?>">
                                                <?php echo ucfirst($payment_info['status']); ?>
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
                        
                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <?php if (!$success): ?>
                                <a href="pay.php?link=<?php echo $payment_id; ?>" class="btn btn-outline-primary">
                                    <i class="ri-refresh-line me-2"></i>
                                    Try Again
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="ri-shield-check-line me-1"></i>
                                Secure payment powered by KidMate
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