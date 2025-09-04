<?php
include "db.php";
session_start();

// Dummy Admin Authentication
$admin_password = "admin123";  
if (!isset($_SESSION["admin_logged_in"])) {
    if ($_POST["password"] == $admin_password) {
        $_SESSION["admin_logged_in"] = true;
    } else {
        echo '<form method="post"><input type="password" name="password" placeholder="Admin Password" required><button type="submit">Login</button></form>';
        exit();
    }
}

$orders = $conn->query("SELECT * FROM orders");

?>

<h2>Admin Dashboard</h2>
<table border="1">
    <tr><th>Order Details</th><th>Amount</th><th>Status</th><th>Payment Link</th></tr>
    <?php while ($order = $orders->fetch_assoc()): ?>
        <tr>
            <td><?= $order["order_details"] ?></td>
            <td><?= $order["amount"] ?></td>
            <td><?= $order["status"] ?></td>
            <td><a href="<?= $order["payment_link"] ?>">View Link</a></td>
        </tr>
    <?php endwhile; ?>
</table>
