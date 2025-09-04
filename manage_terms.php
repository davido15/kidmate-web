<?php
include "session.php";
include "db.php";

// Fetch all terms
$query = "SELECT * FROM terms ORDER BY start_date DESC";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$terms = [];
while ($row = $result->fetch_assoc()) {
    $terms[] = $row;
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
                        <h3>Terms</h3>
                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Terms</a></div>
                  </div>
               </div>
            </div>
            
            <div class="row">
               <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Terms Records</h4>
                            <div class="card-header flex-row">
                                <a class="btn btn-primary" href="add_terms.php">
                                    <span><i class="bi bi-plus"></i></span>Add Term
                                </a>
                                <a class="btn btn-success ms-2" href="upload_terms.php">
                                    <span><i class="ri-upload-line"></i></span>Upload Excel
                                </a>
                                <a class="btn btn-info ms-2" href="templates/terms_template.csv" download>
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
                                                <th>Term Name</th>
                                                <th>Term Code</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($terms as $term): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($term['id']); ?></td>
                                                <td><?php echo htmlspecialchars($term['term_name']); ?></td>
                                                <td><?php echo htmlspecialchars($term['term_code']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($term['start_date'])); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($term['end_date'])); ?></td>
                                                <td>
                                                    <?php 
                                                    $start = new DateTime($term['start_date']);
                                                    $end = new DateTime($term['end_date']);
                                                    $duration = $start->diff($end);
                                                    echo $duration->days . ' days';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $today = new DateTime();
                                                    $start_date = new DateTime($term['start_date']);
                                                    $end_date = new DateTime($term['end_date']);
                                                    
                                                    if ($today >= $start_date && $today <= $end_date) {
                                                        echo '<span class="badge bg-success">Current</span>';
                                                    } elseif ($today < $start_date) {
                                                        echo '<span class="badge bg-warning">Upcoming</span>';
                                                    } else {
                                                        echo '<span class="badge bg-secondary">Completed</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="edit_terms.php?id=<?php echo $term['id']; ?>" class="btn btn-sm btn-primary me-2">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="delete_terms.php?id=<?php echo $term['id']; ?>" class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this term?')">
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