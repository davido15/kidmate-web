<?php
include 'db.php'; // adjust path as needed
include 'toast.php'; // adjust path as needed

$errorMessage = "";  
$successMessage = "";

/**
 * Simple debug helper to output the SQL and its parameters.
 */
function debugQuery(string $query, array $params): void {
    echo "<pre>";
    echo "Executing SQL: " . $query . "\n";
    echo "With params: " . json_encode($params) . "\n";
    echo "</pre>";
}

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($student_id <= 0) {
    die("Student ID is required and must be a positive integer.");
}

// === Step 1: Fetch student info ===
$query1 = "SELECT * FROM kids WHERE id = ?";

$stmt = $conn->prepare($query1);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}

// === Step 2: Fetch parent name using parent_id ===
$parent_name = "N/A";
if (!empty($student['parent_id'])) {
    $parent_id = intval($student['parent_id']);
    $query2 = "SELECT * FROM parents WHERE id = ?";
  
    $parentStmt = $conn->prepare($query2);
    $parentStmt->bind_param("i", $parent_id);
    $parentStmt->execute();
    $parentResult = $parentStmt->get_result();
    $parent = $parentResult->fetch_assoc();
    $parentStmt->close();
    if ($parent && !empty($parent['name'])) {
        $parent_name = $parent['name'];
        $parent_contact = $parent['phone'];
    }
}

// === Step 3: Fetch pickup persons ===
$query3 = "SELECT * FROM pickup_persons WHERE kid_id = ?";

$pickupStmt = $conn->prepare($query3);
$pickupStmt->bind_param("i", $student_id);
$pickupStmt->execute();
$pickupResult = $pickupStmt->get_result();
$pickupPersons = [];
while ($row = $pickupResult->fetch_assoc()) {
    $pickupPersons[] = $row['name'];
}
$pickupStmt->close();

// === Step 4: Handle update on POST ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_name = trim($_POST['name'] ?? '');
    
    if ($new_name) {
        $query = "UPDATE kids SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        // Use bind_param for mysqli
        $stmt->bind_param("si", $new_name, $student_id); // "s" for string, "i" for integer
        $stmt->execute();

        if ($stmt->affected_rows > 0) {

            
           $successMessage = "Student Record Updated Successfully";
           
        } else {
            $errorMessage = "Update failed or no changes made.";
        }
    } else {
        echo "Name cannot be empty.";
    }
}

?>

<?php include 'header.php'; ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Edit Student</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Update Student Record</h4>
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
                                            <label class="form-label">Student Name</label>
                                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Parent Contact</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($parent_contact) ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Parent Name</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($parent_name) ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Pickup Person(s)</label>
                                            <textarea class="form-control" rows="3" readonly><?= implode(", ", array_map('htmlspecialchars', $pickupPersons)) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-primary">Update Student</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
