<?php
include 'db.php';
include 'session.php';

// Get grade ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: manage_grades.php?error=Invalid grade ID");
    exit;
}

// Check if grade exists
$stmt = $conn->prepare("SELECT id, subject, grade FROM grades WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$grade = $result->fetch_assoc();

if (!$grade) {
    header("Location: manage_grades.php?error=Grade record not found");
    exit;
}

// Delete the grade record
$deleteStmt = $conn->prepare("DELETE FROM grades WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    header("Location: manage_grades.php?success=Grade record deleted successfully");
} else {
    header("Location: manage_grades.php?error=Failed to delete grade record");
}

$stmt->close();
$deleteStmt->close();
$conn->close();
?>