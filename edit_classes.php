<?php
include "session.php";
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: manage_classes.php?error=Invalid class ID");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $class_name = trim($_POST['class_name'] ?? '');
    $class_code = trim($_POST['class_code'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (!empty($class_name) && !empty($class_code)) {
        // Check if class code already exists for other classes
        $check_query = "SELECT id FROM classes WHERE class_code = ? AND id != ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("si", $class_code, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $errorMessage = "Class code already exists. Please use a different code.";
        } else {
            $query = "UPDATE classes SET class_name = ?, class_code = ?, description = ?, is_active = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssii", $class_name, $class_code, $description, $is_active, $id);
            
            if ($stmt->execute()) {
                header("Location: manage_classes.php?success=Class updated successfully!");
                exit;
            } else {
                $errorMessage = "Failed to update class: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    } else {
        $errorMessage = "Class name and class code are required.";
    }
}

// Fetch class data
$query = "SELECT * FROM classes WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();

if (!$class) {
    header("Location: manage_classes.php?error=Class not found");
    exit;
}

$stmt->close();
?>

<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Edit Class</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Class: <?php echo htmlspecialchars($class['class_name']); ?></h4>
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
                                            <label class="form-label">Class Name <span class="text-danger">*</span></label>
                                            <input type="text" name="class_name" class="form-control" required 
                                                   value="<?php echo htmlspecialchars($_POST['class_name'] ?? $class['class_name']); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Class Code <span class="text-danger">*</span></label>
                                            <input type="text" name="class_code" class="form-control" required 
                                                   value="<?php echo htmlspecialchars($_POST['class_code'] ?? $class['class_code']); ?>"
                                                   placeholder="e.g., C001, C002">
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="3" 
                                                      placeholder="Enter class description"><?php echo htmlspecialchars($_POST['description'] ?? $class['description'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                                       <?php echo (($_POST['is_active'] ?? $class['is_active']) ? 'checked' : ''); ?>>
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
                                            <a href="manage_classes.php" class="btn btn-secondary me-2">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Update Class</button>
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