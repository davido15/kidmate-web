<?php
include 'db.php';
include 'toast.php';

// Get pickup ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Pickup ID is required and must be a positive integer.");
}

// Check if pickup_persons table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'pickup_persons'");
if ($tableCheck->num_rows == 0) {
    die("Pickup persons table does not exist.");
}

// Fetch pickup data
$query = "SELECT * FROM pickup_persons WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$pickup = $result->fetch_assoc();
$stmt->close();

if (!$pickup) {
    die("Pickup record with ID $id not found.");
}

// Prepare values
$pickup_name = $pickup['name'];
$pickup_id = $pickup['pickup_id'];
$image_path = $pickup['image'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_name = trim($_POST['name'] ?? '');
    $new_pickup_id = trim($_POST['pickup_id'] ?? '');
    
    if (!empty($new_name) && !empty($new_pickup_id)) {
        // Handle image upload
        $new_image_path = $image_path; // Keep existing image by default
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/pickup/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $new_filename = 'pickup_' . $id . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $new_image_path = $upload_path;
                    
                    // Delete old image if it exists and is different
                    if (!empty($image_path) && $image_path !== $new_image_path && file_exists($image_path)) {
                        unlink($image_path);
                    }
                } else {
                    $errorMessage = "Failed to upload image. Please try again.";
                }
            } else {
                $errorMessage = "Invalid file type. Please upload JPG, JPEG, PNG, or GIF files only.";
            }
        }
        
        if (!isset($errorMessage)) {
            $updateQuery = "UPDATE pickup_persons SET name = ?, pickup_id = ?, image = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("sssi", $new_name, $new_pickup_id, $new_image_path, $id);
            
            if ($updateStmt->execute()) {
                $successMessage = "Pickup person updated successfully!";
                // Refresh the data
                $pickup['name'] = $new_name;
                $pickup['pickup_id'] = $new_pickup_id;
                $pickup['image'] = $new_image_path;
                $pickup_name = $new_name;
                $pickup_id = $new_pickup_id;
                $image_path = $new_image_path;
            } else {
                $errorMessage = "Failed to update pickup person: " . $updateStmt->error;
            }
            $updateStmt->close();
        }
    } else {
        $errorMessage = "Name and Pickup ID are required.";
    }
}

?>

<?php include 'header.php'; ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Edit Pickup Person</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Update Pickup Person Record</h4>
                        </div>
                        <div class="card-body">

                        <?php
                            // Initialize message variables
                            $errorMessage = $errorMessage ?? '';
                            $successMessage = $successMessage ?? '';
                            
                            if ($errorMessage) {
                                toastError($errorMessage);
                            }
                            if ($successMessage) {
                                toastSuccess($successMessage);
                            }
                            ?>
                          
                          <form method="POST" enctype="multipart/form-data">
                          <div class="mb-3">
        <label class="form-label">Pickup Name</label>
        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($pickup_name) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Pickup ID</label>
        <input type="text" class="form-control" name="pickup_id" value="<?= htmlspecialchars($pickup_id) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Current Image</label><br>
        <?php if (!empty($image_path)): ?>
            <img src="<?= htmlspecialchars($image_path) ?>" alt="Pickup Image" class="img-fluid rounded mb-2" style="max-width: 300px;">
        <?php else: ?>
            <p class="text-muted">No image available</p>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Upload New Image (Optional)</label>
        <input type="file" class="form-control" name="image" accept="image/*" id="imageInput" onchange="previewImage(this)">
        <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG, GIF. Leave empty to keep current image.</small>
        <div id="imagePreview" class="mt-2" style="display: none;">
            <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-width: 300px;">
        </div>
    </div>

    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }
    </script>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Update Pickup Person</button>
        <a href="manage_pickups.php" class="btn btn-secondary">Back to List</a>
    </div>
                          </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
