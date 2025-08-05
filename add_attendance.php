<?php
include "session.php";
include 'db.php';

// Fetch students for dropdown
$students_query = "SELECT k.id, k.name, p.name as parent_name FROM kids k LEFT JOIN parents p ON k.parent_id = p.id ORDER BY k.name";
$students_result = $conn->query($students_query);
$students = [];
if ($students_result) {
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selected_student_id = trim($_POST['student_id'] ?? '');
    $date = trim($_POST['date'] ?? date('Y-m-d'));
    $check_in_time = trim($_POST['check_in_time'] ?? '');
    $check_out_time = trim($_POST['check_out_time'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    
    if (!empty($selected_student_id) && !empty($status)) {
        // Get student and parent information
        $student_query = "SELECT k.name as child_name, p.name as parent_name FROM kids k LEFT JOIN parents p ON k.parent_id = p.id WHERE k.id = ?";
        $student_stmt = $conn->prepare($student_query);
        $student_stmt->bind_param("i", $selected_student_id);
        $student_stmt->execute();
        $student_result = $student_stmt->get_result();
        $student_data = $student_result->fetch_assoc();
        
        if ($student_data) {
            // Generate unique IDs
            $attendance_id = 'ATT' . uniqid();
            $child_id = 'CHILD' . $selected_student_id;
            $parent_id = 'PARENT' . uniqid();
            
            // Format check-in and check-out times
            $check_in_datetime = !empty($check_in_time) ? $date . ' ' . $check_in_time . ':00' : null;
            $check_out_datetime = !empty($check_out_time) ? $date . ' ' . $check_out_time . ':00' : null;
            
            $query = "INSERT INTO attendance (attendance_id, child_id, child_name, parent_id, parent_name, date, check_in_time, check_out_time, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssssss", $attendance_id, $child_id, $student_data['child_name'], $parent_id, $student_data['parent_name'], $date, $check_in_datetime, $check_out_datetime, $status, $notes);
            
            if ($stmt->execute()) {
                header("Location: manage_attendance.php?success=Attendance record added successfully!");
                exit;
            } else {
                $errorMessage = "Failed to add attendance record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errorMessage = "Selected student not found.";
        }
        $student_stmt->close();
    } else {
        $errorMessage = "Student and status are required.";
    }
}
?>

<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Add Attendance Record</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add New Attendance Record</h4>
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
                                            <label class="form-label">Select Student</label>
                                            <select name="student_id" class="form-select" required>
                                                <option value="">-- Select Student --</option>
                                                <?php foreach ($students as $student): ?>
                                                    <option value="<?php echo $student['id']; ?>">
                                                        <?php echo htmlspecialchars($student['name']); ?> 
                                                        (<?php echo htmlspecialchars($student['parent_name'] ?? 'No Parent'); ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Date</label>
                                            <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="">-- Select Status --</option>
                                                <option value="Present">Present</option>
                                                <option value="Absent">Absent</option>
                                                <option value="Late">Late</option>
                                                <option value="Checked In">Checked In</option>
                                                <option value="Checked Out">Checked Out</option>
                                                <option value="Sick">Sick</option>
                                                <option value="Excused">Excused</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Check In Time</label>
                                            <input type="time" class="form-control" name="check_in_time" placeholder="HH:MM">
                                            <small class="form-text text-muted">Leave empty if not applicable</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Check Out Time</label>
                                            <input type="time" class="form-control" name="check_out_time" placeholder="HH:MM">
                                            <small class="form-text text-muted">Leave empty if not applicable</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea class="form-control" name="notes" placeholder="Enter any additional notes"></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary">Add Attendance Record</button>
                                            <a href="manage_attendance.php" class="btn btn-secondary">Back to List</a>
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
