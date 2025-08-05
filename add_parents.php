
<?php
include 'db.php';

// Fetch all users for the dropdown
$users_query = "SELECT id, name, email FROM users ORDER BY name";
$users_result = $conn->query($users_query);
$users = [];
if ($users_result) {
    while ($row = $users_result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    // Handle database error silently - users dropdown will be empty
    $users = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $parent_name = trim($_POST['parent_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $occupation = trim($_POST['occupation'] ?? '');
    $relationship = trim($_POST['relationship'] ?? '');
    $user_email = trim($_POST['user_email'] ?? '');
    // Convert empty string to null for foreign key constraint
    if (empty($user_email)) {
        $user_email = null;
    }

    if (empty($parent_name) || empty($phone_number) || empty($relationship)) {
        $error_message = "Please fill in Parent Name, Phone Number, and Relationship.";
    } else {
        // Only proceed with database insertion if validation passes
        $stmt = $conn->prepare("
            INSERT INTO parents (name, phone, address, occupation, relationship, user_email)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            $error_message = "Database error occurred. Please try again.";
        } else {
            $stmt->bind_param("ssssss", $parent_name, $phone_number, $address, $occupation, $relationship, $user_email);

            if ($stmt->execute()) {
                $success_message = "Parent added successfully!";
            } else {
                // Check if it's a duplicate user_email error
                if (strpos($stmt->error, 'Duplicate entry') !== false && strpos($stmt->error, 'user_email') !== false) {
                    $error_message = "This user is already linked to another parent. Each user can only be linked to one parent.";
                } else {
                    $error_message = "Failed to add parent. Please try again.";
                }
            }
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Add Parent</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add Parent Record</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($success_message)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo htmlspecialchars($success_message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo htmlspecialchars($error_message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
        <form method="POST" class="invoice-form">
                                <div class="row">
                                    <div class="col-xl-6">
                                    <div class="mb-3">
                <label class="form-label">Parent Name</label>
                <input type="text" class="form-control" name="parent_name" placeholder="Enter parent's full name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" class="form-control" name="phone_number" placeholder="Enter contact number" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" placeholder="Enter email address">
            </div>
            <div class="mb-3">
                <label class="form-label">Residential Address</label>
                <input type="text" class="form-control" name="address" placeholder="Enter home address">
            </div>
            <div class="mb-3">
                <label class="form-label">Occupation</label>
                <input type="text" class="form-control" name="occupation" placeholder="Enter parent's occupation">
            </div>
            <div class="mb-3">
                <label class="form-label">Relationship to Parent</label>
                <select class="form-control" name="relationship" required>
                    <option value="">Select</option>
                    <option value="Mother">Mother</option>
                    <option value="Father">Father</option>
                    <option value="Guardian">Guardian</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Link to User (Optional)</label>
                <select class="form-control" name="user_email">
                    <option value="">Select User Email (Optional)</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['email']); ?>">
                            <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Select a user to link this parent to. Leave empty if not linking to any user.</small>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Add Parent</button>
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
