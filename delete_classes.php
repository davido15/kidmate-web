<?php
include "session.php";
include 'db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_classes.php?error=Invalid class ID");
    exit;
}

// Check if class exists
$check_query = "SELECT id, class_name FROM classes WHERE id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$class = $check_result->fetch_assoc();

if (!$class) {
    header("Location: manage_classes.php?error=Class not found");
    exit;
}

// Check if class is being used in other tables (optional - add more checks as needed)
// For example, if you have a students table that references classes
$usage_check_query = "SELECT COUNT(*) as count FROM kids WHERE class_id = ?";
$usage_stmt = $conn->prepare($usage_check_query);
$usage_stmt->bind_param("i", $id);
$usage_stmt->execute();
$usage_result = $usage_stmt->get_result();
$usage_count = $usage_result->fetch_assoc()['count'];

if ($usage_count > 0) {
    header("Location: manage_classes.php?error=Cannot delete class. It is being used by " . $usage_count . " student(s)");
    exit;
}

// Delete the class
$delete_query = "DELETE FROM classes WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    header("Location: manage_classes.php?success=Class '" . htmlspecialchars($class['class_name']) . "' deleted successfully!");
} else {
    header("Location: manage_classes.php?error=Failed to delete class: " . $delete_stmt->error);
}

$delete_stmt->close();
$check_stmt->close();
$usage_stmt->close();
$conn->close();
?> 