<?php
include "session.php";
include 'db.php';

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($file_ext === 'xlsx' || $file_ext === 'xls' || $file_ext === 'csv') {
            require 'vendor/autoload.php';
            
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                array_shift($rows); // Skip header
                
                $success_count = 0;
                $error_count = 0;
                
                foreach ($rows as $row) {
                    if (count($row) >= 3) {
                        $student_name = trim($row[0]);
                        $parent_name = trim($row[1]);
                        $parent_phone = trim($row[2]);
                        
                        // Check/create parent
                        $parent_query = "SELECT id FROM parents WHERE phone = ?";
                        $parent_stmt = $conn->prepare($parent_query);
                        $parent_stmt->bind_param("s", $parent_phone);
                        $parent_stmt->execute();
                        $parent_result = $parent_stmt->get_result();
                        
                        if ($parent_result->num_rows > 0) {
                            $parent = $parent_result->fetch_assoc();
                            $parent_id = $parent['id'];
                        } else {
                            $create_parent_query = "INSERT INTO parents (name, phone) VALUES (?, ?)";
                            $create_parent_stmt = $conn->prepare($create_parent_query);
                            $create_parent_stmt->bind_param("ss", $parent_name, $parent_phone);
                            $create_parent_stmt->execute();
                            $parent_id = $conn->insert_id;
                            $create_parent_stmt->close();
                        }
                        $parent_stmt->close();
                        
                        // Insert student
                        $insert_query = "INSERT INTO kids (name, parent_id) VALUES (?, ?)";
                        $insert_stmt = $conn->prepare($insert_query);
                        $insert_stmt->bind_param("si", $student_name, $parent_id);
                        
                        if ($insert_stmt->execute()) {
                            $success_count++;
                        } else {
                            $error_count++;
                        }
                        $insert_stmt->close();
                    }
                }
                
                $message = "Upload completed! Successfully imported $success_count students. Errors: $error_count";
                
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
                        <h3>Upload Students</h3>
                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Students</a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Upload</a></div>
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Upload Students from Excel/CSV</h4>
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
                                <div class="col-md-6">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="excel_file" class="form-label">Select Excel/CSV File</label>
                                            <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                                            <div class="form-text">Supported formats: .xlsx, .xls, .csv</div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-upload-line me-2"></i>Upload Students
                                        </button>
                                        <a href="manage_students.php" class="btn btn-secondary ms-2">
                                            <i class="ri-arrow-left-line me-2"></i>Back to Students
                                        </a>
                                    </form>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">Excel Format Guide</h5>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Required columns (in order):</strong></p>
                                            <ol>
                                                <li><strong>Student Name</strong> - Full name of the student</li>
                                                <li><strong>Parent Name</strong> - Full name of the parent</li>
                                                <li><strong>Parent Phone</strong> - Contact number</li>
                                            </ol>
                                            <p><strong>Note:</strong> If a parent already exists (same phone number), the student will be linked to the existing parent.</p>
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
</div>

<?php include "footer.php" ?> 