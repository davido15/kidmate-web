<?php
include "session.php";
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kid_id = trim($_POST['kid_id'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $grade = trim($_POST['grade'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');
    $comments = trim($_POST['comments'] ?? '');
    $date_recorded = trim($_POST['date_recorded'] ?? date('Y-m-d'));
    
    if (!empty($kid_id) && !empty($subject) && !empty($grade)) {
        $query = "INSERT INTO grades (kid_id, subject, grade, remarks, comments, date_recorded) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssss", $kid_id, $subject, $grade, $remarks, $comments, $date_recorded);
        
        if ($stmt->execute()) {
            header("Location: manage_grades.php?success=Grade added successfully!");
            exit;
        } else {
            $errorMessage = "Failed to add grade: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMessage = "Student, Subject, and Grade are required.";
    }
}

// Fetch students for dropdown
$students_query = "SELECT k.id, k.name, p.name as parent_name FROM kids k LEFT JOIN parents p ON k.parent_id = p.id ORDER BY k.name";
$students_result = $conn->query($students_query);
$students = [];
if ($students_result) {
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>

<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Add Grades</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add New Grade</h4>
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
                                            <label class="form-label">Student</label>
                                            <select name="kid_id" class="form-select" required>
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
                                            <label class="form-label">Subject</label>
                                            <select name="subject" class="form-select" required>
                                                <option value="">-- Select Subject --</option>
                                                <option value="Mathematics">Mathematics</option>
                                                <option value="English">English</option>
                                                <option value="Science">Science</option>
                                                <option value="History">History</option>
                                                <option value="Geography">Geography</option>
                                                <option value="Literature">Literature</option>
                                                <option value="Art">Art</option>
                                                <option value="Physical Education">Physical Education</option>
                                                <option value="Music">Music</option>
                                                <option value="Computer Science">Computer Science</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Grade</label>
                                            <input type="text" class="form-control" name="grade" placeholder="Enter grade (A, B, C, 95, 88, etc.)" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Date Recorded</label>
                                            <input type="date" class="form-control" name="date_recorded" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Remarks</label>
                                            <textarea class="form-control" name="remarks" placeholder="Enter remarks (e.g., Excellent work, Good progress)"></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Comments</label>
                                            <textarea class="form-control" name="comments" placeholder="Enter additional comments"></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary">Add Grade</button>
                                            <a href="manage_grades.php" class="btn btn-secondary">Back to List</a>
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
