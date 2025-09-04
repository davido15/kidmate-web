<?php
include "session.php";
include "db.php";

// Fetch all classes
$classes_query = "SELECT * FROM classes ORDER BY class_name ASC";
$classes_stmt = $conn->prepare($classes_query);
$classes_stmt->execute();
$classes_result = $classes_stmt->get_result();
$classes = [];
while ($row = $classes_result->fetch_assoc()) {
    $classes[] = $row;
}
$classes_stmt->close();

// Fetch all subjects
$subjects_query = "SELECT * FROM subjects ORDER BY subject_name ASC";
$subjects_stmt = $conn->prepare($subjects_query);
$subjects_stmt->execute();
$subjects_result = $subjects_stmt->get_result();
$subjects = [];
while ($row = $subjects_result->fetch_assoc()) {
    $subjects[] = $row;
}
$subjects_stmt->close();

// Fetch all terms
$terms_query = "SELECT * FROM terms ORDER BY start_date DESC";
$terms_stmt = $conn->prepare($terms_query);
$terms_stmt->execute();
$terms_result = $terms_stmt->get_result();
$terms = [];
while ($row = $terms_result->fetch_assoc()) {
    $terms[] = $row;
}
$terms_stmt->close();
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
                        <h3>Academic Management</h3>
                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Academic Management</a></div>
                  </div>
               </div>
            </div>
            
            <div class="row">
               <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Academic Records</h4>
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
                            
                            <!-- Tabs -->
                            <ul class="nav nav-tabs" id="academicTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes" type="button" role="tab">
                                        <i class="ri-inbox-archive-fill me-2"></i>Classes
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">
                                        <i class="ri-keyboard-line me-2"></i>Subjects
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="terms-tab" data-bs-toggle="tab" data-bs-target="#terms" type="button" role="tab">
                                        <i class="ri-book-3-fill me-2"></i>Terms
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="academicTabsContent">
                                <!-- Classes Tab -->
                                <div class="tab-pane fade show active" id="classes" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                                        <h5>Classes Management</h5>
                                        <a class="btn btn-primary btn-sm" href="add_classes.php">
                                            <i class="bi bi-plus"></i> Add Class
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Class Name</th>
                                                    <th>Class Code</th>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($classes as $class): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($class['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                                                    <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($class['class_code']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($class['description'] ?? ''); ?></td>
                                                    <td>
                                                        <span class="badge <?php echo $class['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                                            <?php echo $class['is_active'] ? 'Active' : 'Inactive'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="edit_classes.php?id=<?php echo $class['id']; ?>" class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="delete_classes.php?id=<?php echo $class['id']; ?>" class="btn btn-outline-danger" 
                                                               onclick="return confirm('Delete this class?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Subjects Tab -->
                                <div class="tab-pane fade" id="subjects" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                                        <h5>Subjects Management</h5>
                                        <a class="btn btn-primary btn-sm" href="add_subjects.php">
                                            <i class="bi bi-plus"></i> Add Subject
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Subject Name</th>
                                                    <th>Subject Code</th>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($subjects as $subject): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($subject['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                                    <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($subject['subject_code']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($subject['description'] ?? ''); ?></td>
                                                    <td>
                                                        <span class="badge <?php echo $subject['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                                            <?php echo $subject['is_active'] ? 'Active' : 'Inactive'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="edit_subjects.php?id=<?php echo $subject['id']; ?>" class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="delete_subjects.php?id=<?php echo $subject['id']; ?>" class="btn btn-outline-danger" 
                                                               onclick="return confirm('Delete this subject?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Terms Tab -->
                                <div class="tab-pane fade" id="terms" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                                        <h5>Terms Management</h5>
                                        <a class="btn btn-primary btn-sm" href="add_terms.php">
                                            <i class="bi bi-plus"></i> Add Term
                                        </a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Term Name</th>
                                                    <th>Term Code</th>
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
                                                    <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($term['term_code']); ?></span></td>
                                                    <td>
                                                        <?php 
                                                        $start = new DateTime($term['start_date']);
                                                        $end = new DateTime($term['end_date']);
                                                        $duration = $start->diff($end);
                                                        echo $duration->days . ' days';
                                                        ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?php echo date('M d', strtotime($term['start_date'])); ?> - 
                                                            <?php echo date('M d, Y', strtotime($term['end_date'])); ?>
                                                        </small>
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
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="edit_terms.php?id=<?php echo $term['id']; ?>" class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="delete_terms.php?id=<?php echo $term['id']; ?>" class="btn btn-outline-danger" 
                                                               onclick="return confirm('Delete this term?')">
                                                                <i class="fas fa-trash"></i>
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
</div>

<script>
// Initialize Bootstrap tabs
document.addEventListener('DOMContentLoaded', function() {
    var triggerTabList = [].slice.call(document.querySelectorAll('#academicTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
    });
});
</script>

<?php include "footer.php" ?> 