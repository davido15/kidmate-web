
<?php
include "session.php";
include "db.php";

$sql = "
    SELECT 
        pp.id AS pickup_person_id,
        pp.name AS pickup_person_name,
        pp.pickup_id,
        pp.uuid,
        pp.phone AS pickup_phone,
        pp.dropoff_location,
        k.name AS student_name,
        p.name AS parent_name,
        p.phone AS parent_contact
    FROM 
        pickup_persons pp
    LEFT JOIN 
        kids k ON pp.kid_id = k.id
    LEFT JOIN 
        parents p ON k.parent_id = p.id
    ORDER BY pp.id DESC
";

$result = $conn->query($sql);
?>


<?php include "header.php" ?>

<div id="main-wrapper">

 </div>

 <?php include "sidebar.php"; ?>


    <div class="content-body">
        <div class="container">
            <div class="page-title">
               <div class="row align-items-center justify-content-between">
                  <div class="col-xl-4">
                     <div class="page-title-content">
                        <h3>Pickup Person</h3>

                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Pickup Person</a></div>
                  </div>
               </div>
            </div>
            <div class="row">
             
              
               <div class="col-xl-12">

                  
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Pickup Person</h4>
        <div class="card-header flex-row">
            <a class="btn btn-primary" href="add_pickups.php">
                <span><i class="bi bi-plus"></i></span>Add Pickup Person
            </a>
            <a class="btn btn-success ms-2" href="upload_pickups.php">
                <span><i class="ri-upload-line"></i></span>Upload Excel
            </a>
            <a class="btn btn-info ms-2" href="templates/pickups_template.csv" download>
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
                            <th>Pickup Person Name</th>
                            <th>Pickup ID</th>
                            <th>Phone</th>
                            <th>Drop-off Location</th>
                            <th>Student Name</th>
                            <th>Parent Name</th>
                            <th>Parent Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hasRecords = false;
                        while ($row = $result->fetch_assoc()) { 
                            $hasRecords = true;
                        ?>
                        <tr>
                            <td><?php echo $row['pickup_person_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['pickup_person_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['pickup_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['pickup_phone'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['dropoff_location'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['student_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['parent_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['parent_contact'] ?? 'N/A'); ?></td>
                            <td>
                                <a href="edit_pickups.php?id=<?php echo $row['pickup_person_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete_pickups.php?id=<?php echo $row['pickup_person_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this pickup person?')">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                        
                        <?php if (!$hasRecords): ?>
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No pickup persons found. <a href="add_pickups.php" class="alert-link">Add your first pickup person</a>
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