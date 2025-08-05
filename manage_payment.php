<?php
include "session.php";
include "db.php";
$query2= "
    SELECT 
        id,
        payment_id,
        parent_id,
        child_id,
        amount,
        currency,
        status,
        payment_method,
        description,
        journey_date,
        created_at
    FROM payments
";
$parentStmt = $conn->prepare($query2);

// Check if prepare statement failed
if ($parentStmt === false) {
    die("Prepare failed: " . $conn->error);
}

$parentStmt->execute();
$parentResult = $parentStmt->get_result();

$parents = [];
while ($row = $parentResult->fetch_assoc()) {
    $parents[] = $row;
}
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
                        <h3>Payment Records</h3>
                     </div>
                  </div>
                  <div class="col-auto">
                     <div class="breadcrumbs"><a href="#">Home </a><span><i class="ri-arrow-right-s-line"></i></span><a href="#">Payments</a></div>
                  </div>
               </div>
            </div>

            <div class="row">
               <div class="col-xl-12">
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Payments Records</h4>
        <div class="card-header flex-row">
        <a class="btn btn-primary" href="add_attendance.php"><span><i class="bi bi-plus"></i></span>Add Records</a>
    </div>
                        
                                      
    </div>
    <div class="card-body">
        <div class="Students-content">
            <div class="table-responsive">
            <table class="table" id="mainTable">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Parent ID</th>
                            <th>Child ID</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php foreach ($parents as $row): ?>
    <tr>
        <td>
            <a href="edit_payment.php?id=<?php echo htmlspecialchars($row['payment_id']); ?>">
                <?php echo htmlspecialchars($row['payment_id']); ?>
            </a>
        </td>
        <td><?php echo htmlspecialchars($row['parent_id']); ?></td>
        <td><?php echo htmlspecialchars($row['child_id']); ?></td>
        <td><?php echo htmlspecialchars($row['amount']); ?></td>
        <td><?php echo htmlspecialchars($row['currency']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
        <td><?php echo htmlspecialchars($row['description']); ?></td>
        <td><?php echo htmlspecialchars($row['journey_date']); ?></td>
    
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

<?php include "footer.php" ?>
