<?php
include "session.php";
include 'db.php';

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];
    
    // Check if file was uploaded successfully
    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check file extension
        if ($file_ext === 'xlsx' || $file_ext === 'xls' || $file_ext === 'csv') {
            // Include PhpSpreadsheet library (you may need to install it)
            require 'vendor/autoload.php'; // If using Composer
            
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_tmp);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Skip header row
                array_shift($rows);
                
                $success_count = 0;
                $error_count = 0;
                
                foreach ($rows as $row) {
                    if (count($row) >= 4) {
                        $student_name = trim($row[0]);
                        $subject = trim($row[1]);
                        $grade = trim($row[2]);
                        $remarks = trim($row[3] ?? '');
                        $comments = trim($row[4] ?? '');
                        $date_recorded = trim($row[5] ?? date('Y-m-d'));
                        
                        // Find student ID by name
                        $student_query = "SELECT id FROM kids WHERE name = ?";
                        $student_stmt = $conn->prepare($student_query);
                        $student_stmt->bind_param("s", $student_name);
                        $student_stmt->execute();
                        $student_result = $student_stmt->get_result();
                        
                        if ($student_result->num_rows > 0) {
                            $student = $student_result->fetch_assoc();
                            $kid_id = $student['id'];
                            
                            // Insert grade
                            $insert_query = "INSERT INTO grades (kid_id, subject, grade, remarks, comments, date_recorded) VALUES (?, ?, ?, ?, ?, ?)";
                            $insert_stmt = $conn->prepare($insert_query);
                            $insert_stmt->bind_param("isssss", $kid_id, $subject, $grade, $remarks, $comments, $date_recorded);
                            
                            if ($insert_stmt->execute()) {
                                $success_count++;
                            } else {
                                $error_count++;
                            }
                            $insert_stmt->close();
                        } else {
                            $error_count++;
                        }
                        $student_stmt->close();
                    }
                }
                
                $message = "Upload completed! Successfully imported $success_count grades. Errors: $error_count";
                
            } catch (Exception $e) {
                $error = "Error processing file: " . $e->getMessage();
            }
        } else {
            $error = "Invalid file format. Please upload Excel (.xlsx, .xls) or CSV files only.";
        }
    } else {
        $error = "Error uploading file. Please try again.";
    }
}

// Fetch students for reference
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
                <h4>Upload Grades</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Upload Grades from Excel</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo htmlspecialchars($message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo htmlspecialchars($error); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-xl-8">
                                    <form method="POST" enctype="multipart/form-data" class="upload-form">
                                        <div class="mb-3">
                                            <label class="form-label">Select Excel File <span class="text-danger">*</span></label>
                                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                            <small class="form-text text-muted">Supported formats: .xlsx, .xls, .csv</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-upload-line me-2"></i>Upload Grades
                                            </button>
                                            <a href="manage_grades.php" class="btn btn-secondary ms-2">Back to Grades</a>
                                        </div>
                                    </form>
                                </div>
                                
                                <div class="col-xl-4">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Excel Format Guide</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-2"><strong>Required columns:</strong></p>
                                            <ul class="list-unstyled">
                                                <li><strong>Column A:</strong> Student Name</li>
                                                <li><strong>Column B:</strong> Subject</li>
                                                <li><strong>Column C:</strong> Grade</li>
                                                <li><strong>Column D:</strong> Remarks (optional)</li>
                                                <li><strong>Column E:</strong> Comments (optional)</li>
                                                <li><strong>Column F:</strong> Date (optional, format: YYYY-MM-DD)</li>
                                            </ul>
                                            <hr>
                                            <p class="mb-2"><strong>Example:</strong></p>
                                            <small class="text-muted">
                                                John Doe | Mathematics | A+ | Excellent work | Keep it up | 2024-01-15
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Available Students</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Student Name</th>
                                                    <th>Parent</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($students as $student): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['parent_name'] ?? 'N/A'); ?></td>
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

<?php include "footer.php" ?> 