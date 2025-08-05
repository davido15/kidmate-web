<?php
include 'db.php';
include 'session.php';

// Get attendance ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: manage_attendance.php?error=Invalid attendance ID");
    exit;
}

// Check if attendance record exists
$stmt = $conn->prepare("SELECT id, child_name, date FROM attendance WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$attendance = $result->fetch_assoc();

if (!$attendance) {
    header("Location: manage_attendance.php?error=Attendance record not found");
    exit;
}

// Delete the attendance record
$deleteStmt = $conn->prepare("DELETE FROM attendance WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    header("Location: manage_attendance.php?success=Attendance record deleted successfully");
} else {
    header("Location: manage_attendance.php?error=Failed to delete attendance record");
}

$stmt->close();
$deleteStmt->close();
$conn->close();
?>