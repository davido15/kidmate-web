
<?php
include "session.php";
include "db.php";

// Fetch all grades with student information
$query = "
    SELECT 
        g.id,
        g.kid_id,
        g.subject,
        g.grade,
        g.remarks,
        g.comments,
        g.date_recorded,
        k.name as student_name,
        p.name as parent_name
    FROM 
        grades g
    LEFT JOIN 
        kids k ON g.kid_id = k.id
    LEFT JOIN 
        parents p ON k.parent_id = p.id
    ORDER BY 
        g.date_recorded DESC, g.id DESC
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$grades = [];
while ($row = $result->fetch_assoc()) {
    $grades[] = $row;
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
                        <h3>Grades</h3>
                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Grades</a></div>
                  </div>
               </div>
            </div>
            
            <div class="row">
               <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Grades Records</h4>
                            <div class="card-header flex-row">
                                <a class="btn btn-primary" href="add_grades.php">
                                    <span><i class="bi bi-plus"></i></span>Add Grades
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
                                                <th>Student Name</th>
                                                <th>Parent</th>
                                                <th>Subject</th>
                                                <th>Grade</th>
                                                <th>Remarks</th>
                                                <th>Comments</th>
                                                <th>Date Recorded</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $hasRecords = false;
                                            foreach ($grades as $row): 
                                                $hasRecords = true;
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['student_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($row['parent_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        $grade = $row['grade'];
                                                        if (is_numeric($grade)) {
                                                            if ($grade >= 90) echo 'success';
                                                            elseif ($grade >= 80) echo 'info';
                                                            elseif ($grade >= 70) echo 'warning';
                                                            else echo 'danger';
                                                        } else {
                                                            echo 'secondary';
                                                        }
                                                    ?>">
                                                        <?php echo htmlspecialchars($row['grade']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($row['comments'] ?? ''); ?></td>
                                                <td><?php echo htmlspecialchars($row['date_recorded']); ?></td>
                                                <td>
                                                    <a href="edit_grades.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                    <a href="delete_grades.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this grade record?')">Delete</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            
                                            <?php if (!$hasRecords): ?>
                                            <tr>
                                                <td colspan="9" class="text-center">
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        No grade records found. <a href="add_grades.php" class="alert-link">Add your first grade record</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
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