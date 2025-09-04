<?php
include "session.php";
include "db.php";

// Fetch all subjects
$query = "SELECT * FROM subjects ORDER BY subject_name ASC";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

$stmt->close();
?>

<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
               <div class="row align-items-center justify-content-between">
                  <div class="col-xl-4">
                     <div class="page-title-content">
                        <h3>Subjects</h3>
                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Subjects</a></div>
                  </div>
               </div>
            </div>
            
            <div class="row">
               <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Subjects Records</h4>
                            <div class="card-header flex-row">
                                <a class="btn btn-primary" href="add_subjects.php">
                                    <span><i class="bi bi-plus"></i></span>Add Subject
                                </a>
                                <a class="btn btn-success ms-2" href="upload_subjects.php">
                                    <span><i class="ri-upload-line"></i></span>Upload Excel
                                </a>
                                <a class="btn btn-info ms-2" href="templates/subjects_template.csv" download>
                                    <span><i class="ri-download-line"></i></span>Download Template
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <?php
                            // Display success/error messages
                            if (isset($_GET['success'])) {
                                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        ' . htmlspecialchars($_GET['success']) . '
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>';
                            }
                            if (isset($_GET['error'])) {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        ' . htmlspecialchars($_GET['error']) . '
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>';
                            }
                            ?>
                            
                            <div class="Students-content">
                                <div class="table-responsive">
                                    <table class="table" id="mainTable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Subject Name</th>
                                                <th>Subject Code</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($subjects as $subject): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($subject['id']); ?></td>
                                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                                <td><?php echo htmlspecialchars($subject['description'] ?? ''); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $subject['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo $subject['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($subject['created_at'])); ?></td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="edit_subjects.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-primary me-2">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="delete_subjects.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this subject?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
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
</div>

<?php include "footer.php" ?> 