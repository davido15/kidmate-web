<?php
include "session.php";
include 'db.php'; // adjust path as needed

// === Step 1: Fetch all parents ===
$query2 = "SELECT * FROM parents";
$parentStmt = $conn->prepare($query2);
$parentStmt->execute();
$parentResult = $parentStmt->get_result();

$parents = [];
while ($row = $parentResult->fetch_assoc()) {
    $parents[] = $row;
}

$parentStmt->close();

// === Step 2: Handle update on POST ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_name = trim($_POST['name']);
    $parent_id = trim($_POST['parent_id']);
    
    if (!empty($student_name) && !empty($parent_id)) {
        $query = "INSERT INTO kids (name, parent_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $student_name, $parent_id); // "s" = string, "i" = integer
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: manage_students.php"); // Redirect after successful insert
            exit;
        } else {
            echo "Insert failed or no changes made.";
        }

        $stmt->close();
    } else {
        echo "Name or Parent ID cannot be empty.";
    }
}
?>


<?php include "header.php" ?>

<div id="main-wrapper">
    <?php include "sidebar.php"; ?>

    <div class="content-body">
        <div class="container">
            <div class="page-title">
                <h4>Add Student</h4>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add New Student</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="invoice-form">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="mb-3">
                                            <label class="form-label">Student Name</label>
                                            <input type="text" class="form-control" name="name" placeholder="Enter student name" required>
                                        </div>




        <div class="mb-3">
            <label class="form-label">Select Parent</label>
            <select name="parent_id" class="form-select" required>
                <option value="">-- Select Parent --</option>
                <?php foreach ($parents as $parent): ?>
                    <option value="<?= $parent['id'] ?>"><?= htmlspecialchars($parent['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    



                                      
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <button type="submit" class="btn btn-primary">Add Student</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/twbs/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/scripts.js"></script>
</body>
</html>
