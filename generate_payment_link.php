<?php
include "session.php";
include 'db.php';

$message = '';
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = trim($_POST['student_id'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $currency = trim($_POST['currency'] ?? 'GHS');
    $reason = trim($_POST['reason'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? 'online');
    
    if (!empty($student_id) && !empty($amount) && !empty($reason)) {
        // Generate unique payment link ID
        $payment_link_id = 'PAY_' . time() . '_' . rand(1000, 9999);
        
        // Create payment record in existing payments table
        $query = "INSERT INTO payments (payment_id, parent_id, child_id, amount, currency, status, payment_method, description, journey_date) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?)";
        
        // Get parent_id for the student
        $parent_query = "SELECT parent_id FROM kids WHERE id = ?";
        $parent_stmt = $conn->prepare($parent_query);
        $parent_stmt->bind_param("i", $student_id);
        $parent_stmt->execute();
        $parent_result = $parent_stmt->get_result();
        $parent_data = $parent_result->fetch_assoc();
        $parent_id = $parent_data['parent_id'] ?? null;
        $parent_stmt->close();
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssdssss", $payment_link_id, $parent_id, $student_id, $amount, $currency, $payment_method, $reason, $due_date);
        
        if ($stmt->execute()) {
            $payment_link_url = "https://" . $_SERVER['HTTP_HOST'] . "/KidMate/pay.php?link=" . $payment_link_id;
            $message = "Payment link generated successfully! Link: " . $payment_link_url;
        } else {
            $error = "Failed to generate payment link: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Student, Amount, and Reason are required.";
    }
}

// Fetch students for dropdown
$students_query = "SELECT k.id, k.name, p.name as parent_name FROM kids k LEFT JOIN parents p ON k.parent_id = p.id ORDER BY k.name";
$students_result = $conn->query($students_query);
$students = [];
if ($students_result) {
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Fetch existing payment links (pending payments)
$links_query = "SELECT p.*, k.name as student_name, par.name as parent_name 
                FROM payments p 
                LEFT JOIN kids k ON p.child_id = k.id 
                LEFT JOIN parents par ON p.parent_id = par.id 
                WHERE p.status = 'pending'
                ORDER BY p.created_at DESC";
$links_result = $conn->query($links_query);
$payment_links = [];
if ($links_result) {
    while ($row = $links_result->fetch_assoc()) {
        $payment_links[] = $row;
    }
}
?>

<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Generate Payment Link</h4>
            </div>
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Create Payment Link</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo htmlspecialchars($message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo htmlspecialchars($error); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" class="payment-form">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Student <span class="text-danger">*</span></label>
                                            <select name="student_id" class="form-select" required>
                                                <option value="">-- Select Student --</option>
                                                <?php foreach ($students as $student): ?>
                                                    <option value="<?php echo $student['id']; ?>">
                                                        <?php echo htmlspecialchars($student['name']); ?> 
                                                        (<?php echo htmlspecialchars($student['parent_name'] ?? 'No Parent'); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                                            <input type="number" name="amount" class="form-control" step="0.01" min="0" required 
                                                   value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Currency</label>
                                            <select name="currency" class="form-select">
                                                <option value="GHS" <?php echo ($_POST['currency'] ?? 'GHS') == 'GHS' ? 'selected' : ''; ?>>GHS (Ghana Cedi)</option>
                                                <option value="USD" <?php echo ($_POST['currency'] ?? '') == 'USD' ? 'selected' : ''; ?>>USD (US Dollar)</option>
                                                <option value="EUR" <?php echo ($_POST['currency'] ?? '') == 'EUR' ? 'selected' : ''; ?>>EUR (Euro)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Payment Reason <span class="text-danger">*</span></label>
                                            <textarea name="reason" class="form-control" rows="3" required 
                                                      placeholder="Enter payment reason (e.g., School fees, Books, Uniform, etc.)"><?php echo htmlspecialchars($_POST['reason'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Due Date</label>
                                            <input type="date" name="due_date" class="form-control" 
                                                   value="<?php echo htmlspecialchars($_POST['due_date'] ?? ''); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Payment Method</label>
                                            <select name="payment_method" class="form-select">
                                                <option value="online" <?php echo ($_POST['payment_method'] ?? 'online') == 'online' ? 'selected' : ''; ?>>Online Payment</option>
                                                <option value="mobile_money" <?php echo ($_POST['payment_method'] ?? '') == 'mobile_money' ? 'selected' : ''; ?>>Mobile Money</option>
                                                <option value="bank_transfer" <?php echo ($_POST['payment_method'] ?? '') == 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                                <option value="cash" <?php echo ($_POST['payment_method'] ?? '') == 'cash' ? 'selected' : ''; ?>>Cash</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end">
                                            <a href="manage_payment.php" class="btn btn-secondary me-2">Cancel</a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-link me-2"></i>Generate Payment Link
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Payment Link Guide</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="ri-information-line me-2"></i>How it works:</h6>
                                <ol class="mb-0">
                                    <li>Select a student and enter payment details</li>
                                    <li>Generate a unique payment link</li>
                                    <li>Share the link with parents/guardians</li>
                                    <li>Track payment status in real-time</li>
                                </ol>
                            </div>
                            
                            <div class="alert alert-warning">
                                <h6><i class="ri-alert-line me-2"></i>Important:</h6>
                                <ul class="mb-0">
                                    <li>Payment links are unique and secure</li>
                                    <li>Links expire after payment completion</li>
                                    <li>All transactions are logged and tracked</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Links History -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Payment Links History</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Link ID</th>
                                            <th>Student</th>
                                            <th>Amount</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($payment_links)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No payment links generated yet</td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach ($payment_links as $link): ?>
                                            <tr>
                                                                                <td>
                                    <code><?php echo htmlspecialchars($link['payment_id']); ?></code>
                                </td>
                                                <td><?php echo htmlspecialchars($link['student_name'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($link['currency']); ?> <?php echo number_format($link['amount'], 2); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($link['description']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $link['status'] == 'paid' ? 'success' : 
                                                            ($link['status'] == 'pending' ? 'warning' : 'danger'); 
                                                    ?>">
                                                        <?php echo ucfirst($link['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y H:i', strtotime($link['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                                                                                 <a href="pay.php?link=<?php echo $link['payment_id']; ?>" 
                                                            class="btn btn-outline-primary" target="_blank">
                                                             <i class="ri-external-link-line"></i> View
                                                         </a>
                                                         <button class="btn btn-outline-info" 
                                                                 onclick="copyToClipboard('<?php echo "https://" . $_SERVER['HTTP_HOST'] . "/KidMate/pay.php?link=" . $link['payment_id']; ?>')">
                                                             <i class="ri-file-copy-line"></i> Copy
                                                         </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Payment link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>

<?php include "footer.php" ?> 