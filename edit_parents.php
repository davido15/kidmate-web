<?php
include 'db.php';
include 'session.php';

$errorMessage = "";  
$successMessage = "";

$parent_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($parent_id <= 0) {
    $errorMessage = "Parent ID is required and must be a positive integer.";
} else {
    // Fetch parent info
    $query = "SELECT * FROM parents WHERE id = ?";
    $parentStmt = $conn->prepare($query);
    $parentStmt->bind_param("i", $parent_id);
    $parentStmt->execute();
    $parentResult = $parentStmt->get_result();
    $parent = $parentResult->fetch_assoc();
    $parentStmt->close();

    if (!$parent) {
        $errorMessage = "Parent record not found.";
    }
}

// Fetch users for dropdown
$users_query = "SELECT email, name FROM users ORDER BY name";
$users_result = $conn->query($users_query);
$users = [];
if ($users_result) {
    while ($row = $users_result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle POST update
if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($errorMessage)) {
    $new_name = trim($_POST['name'] ?? '');
    $new_phone = trim($_POST['phone'] ?? '');
    $new_address = trim($_POST['address'] ?? '');
    $new_occupation = trim($_POST['occupation'] ?? '');
    $new_relationship = trim($_POST['relationship'] ?? '');
    $new_user_email = trim($_POST['user_email'] ?? '');

    // Convert empty user_email to NULL
    if (empty($new_user_email)) {
        $new_user_email = null;
    }

    if (empty($new_name)) {
        $errorMessage = "Parent name is required.";
    } else {
        // Build update query
        $query = "UPDATE parents SET name = ?, phone = ?, address = ?, occupation = ?, relationship = ?, user_email = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $new_name, $new_phone, $new_address, $new_occupation, $new_relationship, $new_user_email, $parent_id);
        
        if ($stmt->execute()) {
            $successMessage = "Parent updated successfully!";
            
            // Reload parent data after update for display
            $query2 = "SELECT * FROM parents WHERE id = ?";
            $parentStmt = $conn->prepare($query2);
            $parentStmt->bind_param("i", $parent_id);
            $parentStmt->execute();
            $parentResult = $parentStmt->get_result();
            $parent = $parentResult->fetch_assoc();
            $parentStmt->close();
        } else {
            // Check if it's a duplicate user_email error
            if (strpos($stmt->error, 'Duplicate entry') !== false && strpos($stmt->error, 'user_email') !== false) {
                $errorMessage = "This user is already linked to another parent. Each user can only be linked to one parent.";
            } else {
                $errorMessage = "Failed to update parent: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}
?>

<?php include 'header.php' ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Edit Parent</h4>
                <div class="col-auto">
                     <div class="breadcrumbs link"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="manage_parents.php">Go to Parents Record</a></div>
                  </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Update Parent Record</h4>
                        </div>
                        <div class="card-body">

                            <?php
                            if ($errorMessage) {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        ' . htmlspecialchars($errorMessage) . '
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>';
                            }
                            if ($successMessage) {
                                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        ' . htmlspecialchars($successMessage) . '
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>';
                            }
                            ?>

                            <form method="POST" class="invoice-form">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Parent Name</label>
                                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($parent['name'] ?? '') ?>" required />
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($parent['phone'] ?? '') ?>" />
                                        </div>
                                        

                                        
                                        <div class="mb-3">
                                            <label class="form-label">Residential Address</label>
                                            <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($parent['address'] ?? '') ?>" />
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Occupation</label>
                                            <input type="text" class="form-control" name="occupation" value="<?= htmlspecialchars($parent['occupation'] ?? '') ?>" />
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Relationship to Child</label>
                                            <select class="form-control" name="relationship">
                                                <option value="">Select</option>
                                                <option value="Mother" <?= ($parent['relationship'] ?? '') == 'Mother' ? 'selected' : '' ?>>Mother</option>
                                                <option value="Father" <?= ($parent['relationship'] ?? '') == 'Father' ? 'selected' : '' ?>>Father</option>
                                                <option value="Guardian" <?= ($parent['relationship'] ?? '') == 'Guardian' ? 'selected' : '' ?>>Guardian</option>
                                                <option value="Other" <?= ($parent['relationship'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Link to User (Optional)</label>
                                            <select class="form-control" name="user_email">
                                                <option value="">-- No User Link --</option>
                                                <?php foreach ($users as $user): ?>
                                                    <option value="<?php echo htmlspecialchars($user['email']); ?>" 
                                                            <?= ($parent['user_email'] ?? '') == $user['email'] ? 'selected' : '' ?>>
                                                        <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="form-text text-muted">Link this parent to an existing user account</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary">Update Parent</button>
                                            <a href="manage_parents.php" class="btn btn-secondary">Back to List</a>
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

<?php include 'footer.php' ?>
