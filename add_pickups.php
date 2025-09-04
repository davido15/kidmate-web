<?php
include "session.php";
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $pickup_id = trim($_POST['pickup_id'] ?? '');
    $kid_id = trim($_POST['kid_id'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dropoff_location = trim($_POST['dropoff_location'] ?? '');
    
    if (!empty($name) && !empty($pickup_id)) {
        // Handle image upload
        $image_path = null;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/pickup/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $filename = 'pickup_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = $upload_path;
                } else {
                    $errorMessage = "Failed to upload image. Please try again.";
                }
            } else {
                $errorMessage = "Invalid file type. Please upload JPG, JPEG, PNG, or GIF files only.";
            }
        }
        
        if (!isset($errorMessage)) {
            // Generate UUID for pickup person
            $uuid = uniqid('pickup_', true);
            
            $query = "INSERT INTO pickup_persons (name, pickup_id, kid_id, phone, dropoff_location, image, uuid) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssssss", $name, $pickup_id, $kid_id, $phone, $dropoff_location, $image_path, $uuid);
            
            if ($stmt->execute()) {
                header("Location: manage_pickups.php?success=Pickup person added successfully!");
                exit;
            } else {
                $errorMessage = "Failed to add pickup person: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $errorMessage = "Name and Pickup ID are required.";
    }
}

// Fetch kids for dropdown
$kids_query = "SELECT k.id, k.name, p.name as parent_name FROM kids k LEFT JOIN parents p ON k.parent_id = p.id ORDER BY k.name";
$kids_result = $conn->query($kids_query);
$kids = [];
if ($kids_result) {
    while ($row = $kids_result->fetch_assoc()) {
        $kids[] = $row;
    }
}
?>

<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Add Pickup Person</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add New Pickup Person</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($errorMessage)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo htmlspecialchars($errorMessage); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" enctype="multipart/form-data" class="invoice-form">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Pickup Person Name</label>
                                            <input type="text" class="form-control" name="name" placeholder="Enter pickup person name" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Pickup ID</label>
                                            <input type="text" class="form-control" name="pickup_id" placeholder="Enter pickup ID" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone" placeholder="Enter phone number">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Drop-off Location</label>
                                            <input type="text" class="form-control" name="dropoff_location" placeholder="Enter drop-off location">
                                            <small class="form-text text-muted">Address or location where the child will be dropped off</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Assign to Student (Optional)</label>
                                            <select name="kid_id" class="form-select">
                                                <option value="">-- Select Student --</option>
                                                <?php foreach ($kids as $kid): ?>
                                                    <option value="<?php echo $kid['id']; ?>">
                                                        <?php echo htmlspecialchars($kid['name']); ?> 
                                                        (<?php echo htmlspecialchars($kid['parent_name'] ?? 'No Parent'); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="form-text text-muted">Leave empty if not assigning to a specific student</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Upload Image (Optional)</label>
                                            <input type="file" class="form-control" name="image" accept="image/*" id="imageInput" onchange="previewImage(this)">
                                            <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG, GIF</small>
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
                                            <button type="submit" class="btn btn-primary">Add Pickup Person</button>
                                            <a href="manage_pickups.php" class="btn btn-secondary">Back to List</a>
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
