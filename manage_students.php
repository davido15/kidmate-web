<?php
include "session.php";
include "db.php";


$sql = "
    SELECT 
        k.id AS student_id,
        k.name AS student_name,
        p.name AS parent_name,
        p.phone AS parent_contact,
        pp.name AS pickup_person_name
    FROM 
        kids k
    JOIN 
        parents p ON k.parent_id = p.id
    LEFT JOIN 
        pickup_persons pp ON k.id = pp.kid_id
 
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
                        <h3>Students</h3>
                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Students</a></div>
                  </div>
               </div>
            </div>
            <div class="row">
             
              
               <div class="col-xl-12">

                  
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Students Records</h4>
        <div class="card-header flex-row">
        <a class="btn btn-primary" href="add_students.php"><span><i class="bi bi-plus"></i></span>Add</a>

    </div>
                        
                           
                     
    </div>


    <div class="card-body">
        <div class="Students-content">
            <div class="table-responsive">
            <table class="table" id="mainTable">
                    <thead>
                        <tr>
                            <th>Studentid</th>
                            <th>Student Name</th>
                            <th>Parent</th>
                            <th>Pickup Person</th>
                            <th>Contact </th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            
                        <td>
  <a href="edit_students.php?id=<?php echo $row['student_id']; ?>">
    <?php echo $row['student_id']; ?>
  </a>
</td>                       <td><?php echo $row['student_name']; ?></td>
                            <td><?php echo $row['parent_name']; ?></td>
                            <td><?php echo $row['pickup_person_name']; ?></td>
                            <td><?php echo $row['parent_contact']; ?></td>
                            <td>Active</td>
                        </tr>
                        <?php } ?>
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