<?php
include 'db.php'; // adjust path as needed
include 'toast.php'; // adjust path as needed

$errorMessage = "";  
$successMessage = "";

$grade_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($grade_id <= 0) {
    die("Grade ID is required and must be a positive integer.");
}

// Fetch existing data
$query = "SELECT * FROM grades WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $grade_id);
$stmt->execute();
$result = $stmt->get_result();
$grade = $result->fetch_assoc();
$stmt->close();



if (!$grade) {
    die("Grade not found.");
}
if ($grade_id) {
    $grade_name = $grade['grade'];
    $subject = $grade['subject'];
    $comments = $grade['comments'];
    $remarks = $grade['remarks'];
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_grade = trim($_POST['grade'] ?? '');
    $new_subject = trim($_POST['subject'] ?? '');
    $new_comments = trim($_POST['comments'] ?? '');
    $new_remarks = trim($_POST['remarks'] ?? '');

    $fields = [];
    $params = [];
    $types = "";

    if ($new_grade !== "") {
        $fields[] = "grade = ?";
        $params[] = $new_grade;
        $types .= "s";
    }

    if ($new_subject !== "") {
        $fields[] = "subject = ?";
        $params[] = $new_subject;
        $types .= "s";
    }

    if ($new_comments !== "") {
        $fields[] = "comments = ?";
        $params[] = $new_comments;
        $types .= "s";
    }

    if ($new_remarks !== "") {
        $fields[] = "remarks = ?";
        $params[] = $new_remarks;
        $types .= "s";
    }

    if (empty($fields)) {
        die("No data provided to update.");
    }

    // Add grade ID to the end
    $params[] = $grade_id;
    $types .= "i";

    $update_query = "UPDATE grades SET " . implode(", ", $fields) . " WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param($types, ...$params);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        $successMessage="Grade data updated successfully.";
    } else {
        $errorMessage="No changes made or update failed.";
    }

    $update_stmt->close();
    $conn->close();
}
?>

<?php include 'header.php' ?>

<body class="dashboard">

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Edit Grades</h4>
                <div class="col-auto">
                     <div class="breadcrumbs link"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="manage_grades.php">Go to Grades Record</a></div>
                  </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Update Pareent Record</h4>
                        </div>
                        <div class="card-body">

                        <?php
                            if ($errorMessage) {
                                toastError($errorMessage);
                            }
                            if ($successMessage) {
                                toastSuccess($successMessage);
                            }
                            ?>

                            <form method="POST" class="invoice-form">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Grades</label>
                                            <input type="text" class="form-control" name="grade" value="<?= htmlspecialchars($grade_name) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">subject</label>
                                            <input type="text" class="form-control" name="subject" value="<?= htmlspecialchars($subject) ?>" >
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Remarks</label>
                                            <input type="text" class="form-control" name="remarks" value="<?= htmlspecialchars($remarks) ?>" >
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Comments</label>
                                            <input type="text" class="form-control"  name="comments" value="<?= htmlspecialchars($comments) ?>" >
                                        </div>
                                     
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-primary">Update grade</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php' ?>
