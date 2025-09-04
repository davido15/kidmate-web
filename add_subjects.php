<?php
include "session.php";
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_name = trim($_POST['subject_name'] ?? '');
    $subject_code = trim($_POST['subject_code'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (!empty($subject_name) && !empty($subject_code)) {
        // Check if subject code already exists
        $check_query = "SELECT id FROM subjects WHERE subject_code = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $subject_code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $errorMessage = "Subject code already exists. Please use a different code.";
        } else {
            $query = "INSERT INTO subjects (subject_name, subject_code, description, is_active) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssi", $subject_name, $subject_code, $description, $is_active);
            
            if ($stmt->execute()) {
                header("Location: manage_subjects.php?success=Subject added successfully!");
                exit;
            } else {
                $errorMessage = "Failed to add subject: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    } else {
        $errorMessage = "Subject name and subject code are required.";
    }
}
?>

<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Add Subject</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add New Subject</h4>
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
                                            <label class="form-label">Subject Name <span class="text-danger">*</span></label>
                                            <input type="text" name="subject_name" class="form-control" required 
                                                   value="<?php echo htmlspecialchars($_POST['subject_name'] ?? ''); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Subject Code <span class="text-danger">*</span></label>
                                            <input type="text" name="subject_code" class="form-control" required 
                                                   value="<?php echo htmlspecialchars($_POST['subject_code'] ?? ''); ?>"
                                                   placeholder="e.g., MATH, ENG, SCI">
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="3" 
                                                      placeholder="Enter subject description"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
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
                                            <a href="manage_subjects.php" class="btn btn-secondary me-2">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Add Subject</button>
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