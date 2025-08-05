<?php 
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION["email"];

?>