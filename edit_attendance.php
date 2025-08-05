<?php
include 'db.php'; // adjust path as needed
include 'toast.php';

$errorMessage = "";  
$successMessage = "";
// Get the attendance ID from the query string
$attendance_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($attendance_id <= 0) {
    die("Attendance ID is required and must be a positive integer.");
}

// Fetch existing attendance data
$query = "SELECT * FROM attendance WHERE attendance_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $attendance_id);
$stmt->execute();
$result = $stmt->get_result();
$attendance = $result->fetch_assoc();
$stmt->close();

if (!$attendance) {
    die("Attendance record not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_status = trim($_POST['status'] ?? '');
    $new_notes = trim($_POST['notes'] ?? '');

    $fields = [];
    $params = [];
    $types = "";

    if ($new_status !== "") {
        $fields[] = "status = ?";
        $params[] = $new_status;
        $types .= "s";
    }

    if ($new_notes !== "") {
        $fields[] = "notes = ?";
        $params[] = $new_notes;
        $types .= "s";
    }

    if (empty($fields)) {
        die("No data provided to update.");
    }

    $params[] = $attendance_id;
    $types .= "i";

    $update_query = "UPDATE attendance SET " . implode(", ", $fields) . " WHERE attendance_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param($types, ...$params);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {


$successMessage = "Attendance record updated successfully.";
       
    } else {
        $errorMessage ="No changes made or update failed.";
    }

    $update_stmt->close();
    $conn->close();
}
?>

<?php include "header.php"; ?>


<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Edit grades</h4>
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
                                            <label class="form-label">Student Id</label>
                                            <input type="text" class="form-control" name="grade" value="<?= htmlspecialchars($attendance['kid_id']) ?>" required readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <input type="text" class="form-control" name="notes" value="<?= htmlspecialchars($attendance['notes']) ?>" >
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <input type="text" class="form-control" name="status" value="<?= htmlspecialchars($attendance['status']) ?>" >
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

<?php include "footer.php"; ?>
