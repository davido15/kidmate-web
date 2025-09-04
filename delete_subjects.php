<?php
include "session.php";
include 'db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_subjects.php?error=Invalid subject ID");
    exit;
}

// Check if subject exists
$check_query = "SELECT id, subject_name FROM subjects WHERE id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$subject = $check_result->fetch_assoc();

if (!$subject) {
    header("Location: manage_subjects.php?error=Subject not found");
    exit;
}

// Check if subject is being used in grades table
$usage_check_query = "SELECT COUNT(*) as count FROM grades WHERE subject = (SELECT subject_name FROM subjects WHERE id = ?)";
$usage_stmt = $conn->prepare($usage_check_query);
$usage_stmt->bind_param("i", $id);
$usage_stmt->execute();
$usage_result = $usage_stmt->get_result();
$usage_count = $usage_result->fetch_assoc()['count'];

if ($usage_count > 0) {
    header("Location: manage_subjects.php?error=Cannot delete subject. It is being used in " . $usage_count . " grade record(s)");
    exit;
}

// Delete the subject
$delete_query = "DELETE FROM subjects WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    header("Location: manage_subjects.php?success=Subject '" . htmlspecialchars($subject['subject_name']) . "' deleted successfully!");
} else {
    header("Location: manage_subjects.php?error=Failed to delete subject: " . $delete_stmt->error);
}

$delete_stmt->close();
$check_stmt->close();
$usage_stmt->close();
$conn->close();
?> 