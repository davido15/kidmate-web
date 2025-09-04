<?php
$servername = "localhost";
$username = "root"; // Change if necessary
$password = "root"; // Change if necessary
$dbname = "kidmate_db";
$dbname_live="mIN?mQ]S5";

// Create Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
