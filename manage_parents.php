
<?php
include "session.php";
include "db.php";


// === Step 1: Fetch all parents ===
$query2 = "SELECT * FROM parents";
$parentStmt = $conn->prepare($query2);
$parentStmt->execute();
$parentResult = $parentStmt->get_result();

$parents = [];
while ($row = $parentResult->fetch_assoc()) {
    $parents[] = $row;
}
?>

<?php include "header.php"; ?>

<div id="main-wrapper">

 </div>

 <?php include "sidebar.php"; ?>


    <div class="content-body">
        <div class="container">
            <div class="page-title">
               <div class="row align-items-center justify-content-between">
                  <div class="col-xl-4">
                     <div class="page-title-content">
                        <h3>Parents</h3>
                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Parents</a></div>
                  </div>
               </div>
            </div>
            <div class="row">
                
<div class="col-xl-12">
 
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Parents Records</h4>
        <div class="card-header flex-row">
            <a class="btn btn-primary" href="add_parents.php">
                <span><i class="bi bi-plus"></i></span>Add Parents
            </a>
            <a class="btn btn-success ms-2" href="upload_parents.php">
                <span><i class="ri-upload-line"></i></span>Upload Excel
            </a>
            <a class="btn btn-info ms-2" href="templates/parents_template.csv" download>
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
        <table class="table" id="parentTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Parent Name</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Occupation</th>
                <th>Relationship</th>
                <th>Linked User</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $hasRecords = false;
            foreach ($parents as $row): 
                $hasRecords = true;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['occupation'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['relationship'] ?? '') ?></td>
                <td>
                    <?php if (!empty($row['user_email'])): ?>
                        <span class="badge bg-success">Linked</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Not Linked</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_parents.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_parents.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this parent?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (!$hasRecords): ?>
            <tr>
                <td colspan="8" class="text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No parent records found. <a href="add_parents.php" class="alert-link">Add your first parent</a>
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
</div>




<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/twbs/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/chartjs/chartjs.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/chartjs-line-init.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/chartjs-donut.js"></script>
<script src="https://cdn.jsdelivr.net/gh/perfect-scrollbar/perfect-scrollbar@1.5.0/dist/perfect-scrollbar.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/vendor/circle-progress/circle-progress.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/circle-progress-init.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/chartjs-bar-init.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davido15/pozy-static@refs/heads/main/js/plugins/chartjs-investment.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="https://cdn.datatables.net/2.3.1/js/dataTables.js"></script>
<!-- DataTables Init -->
<script>
 
  $(document).ready(function() {
    $('#parentTable').DataTable();
  });



</script>




</body>


</html>