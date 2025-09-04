<?php
include "session.php";
include "db.php";

// Fetch all attendance records
$query = "
    SELECT 
        id,
        attendance_id,
        child_id,
        child_name,
        parent_id,
        parent_name,
        date,
        check_in_time,
        check_out_time,
        status,
        notes,
        created_at
    FROM 
        attendance
    ORDER BY 
        date DESC, created_at DESC
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

$attendance_records = [];
while ($row = $result->fetch_assoc()) {
    $attendance_records[] = $row;
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
                        <h3>Attendance Records</h3>
                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Attendance</a></div>
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Attendance Records</h4>
                            <div class="card-header flex-row">
                                <a class="btn btn-primary" href="add_attendance.php">
                                    <span><i class="bi bi-plus"></i></span>Add Records
                                </a>
                                <a class="btn btn-success ms-2" href="upload_attendance.php">
                                    <span><i class="ri-upload-line"></i></span>Upload Excel
                                </a>
                                <a class="btn btn-info ms-2" href="templates/attendance_template.csv" download>
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
                                                <th>Student Name</th>
                                                <th>Parent Name</th>
                                                <th>Date</th>
                                                <th>Check In</th>
                                                <th>Check Out</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $hasRecords = false;
                                            foreach ($attendance_records as $row): 
                                                $hasRecords = true;
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['child_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($row['parent_name'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                                <td>
                                                    <?php 
                                                    if (!empty($row['check_in_time'])) {
                                                        echo date('H:i', strtotime($row['check_in_time']));
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if (!empty($row['check_out_time'])) {
                                                        echo date('H:i', strtotime($row['check_out_time']));
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        $status = strtolower($row['status']);
                                                        if ($status == 'present' || $status == 'checked in') echo 'success';
                                                        elseif ($status == 'absent') echo 'danger';
                                                        elseif ($status == 'late') echo 'warning';
                                                        elseif ($status == 'checked out') echo 'info';
                                                        else echo 'secondary';
                                                    ?>">
                                                        <?php echo htmlspecialchars($row['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['notes'] ?? ''); ?></td>
                                                <td>
                                                    <a href="edit_attendance.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                    <a href="delete_attendance.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this attendance record?')">Delete</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            
                                            <?php if (!$hasRecords): ?>
                                            <tr>
                                                <td colspan="9" class="text-center">
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        No attendance records found. <a href="add_attendance.php" class="alert-link">Add your first attendance record</a>
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
