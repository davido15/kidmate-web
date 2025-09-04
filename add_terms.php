<?php
include "session.php";
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $term_name = trim($_POST['term_name'] ?? '');
    $term_code = trim($_POST['term_code'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (!empty($term_name) && !empty($term_code) && !empty($start_date) && !empty($end_date)) {
        // Validate dates
        if (strtotime($start_date) >= strtotime($end_date)) {
            $errorMessage = "End date must be after start date.";
        } else {
            // Check if term code already exists
            $check_query = "SELECT id FROM terms WHERE term_code = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("s", $term_code);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                $errorMessage = "Term code already exists. Please use a different code.";
            } else {
                $query = "INSERT INTO terms (term_name, term_code, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssi", $term_name, $term_code, $start_date, $end_date, $is_active);
                
                if ($stmt->execute()) {
                    header("Location: manage_terms.php?success=Term added successfully!");
                    exit;
                } else {
                    $errorMessage = "Failed to add term: " . $stmt->error;
                }
                $stmt->close();
            }
            $check_stmt->close();
        }
    } else {
        $errorMessage = "All fields are required.";
    }
}
?>

<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Add Term</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add New Term</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($errorMessage)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo htmlspecialchars($errorMessage); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" class="invoice-form">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Term Name <span class="text-danger">*</span></label>
                                            <input type="text" name="term_name" class="form-control" required 
                                                   value="<?php echo htmlspecialchars($_POST['term_name'] ?? ''); ?>"
                                                   placeholder="e.g., First Term, Second Term">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Term Code <span class="text-danger">*</span></label>
                                            <input type="text" name="term_code" class="form-control" required 
                                                   value="<?php echo htmlspecialchars($_POST['term_code'] ?? ''); ?>"
                                                   placeholder="e.g., T1, T2, T3">
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                            <input type="date" name="start_date" class="form-control" required 
                                                   value="<?php echo htmlspecialchars($_POST['start_date'] ?? ''); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                                            <input type="date" name="end_date" class="form-control" required 
                                                   value="<?php echo htmlspecialchars($_POST['end_date'] ?? ''); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                                       <?php echo (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end">
                                            <a href="manage_terms.php" class="btn btn-secondary me-2">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Add Term</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php" ?> 