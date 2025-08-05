<?php
include 'db.php';
include 'session.php';

// Get pickup ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: manage_pickups.php?error=Invalid pickup ID");
    exit;
}

// Check if pickup person exists
$stmt = $conn->prepare("SELECT id, name FROM pickup_persons WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$pickup = $result->fetch_assoc();

if (!$pickup) {
    header("Location: manage_pickups.php?error=Pickup person not found");
    exit;
}

// Delete the pickup person
$deleteStmt = $conn->prepare("DELETE FROM pickup_persons WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    header("Location: manage_pickups.php?success=Pickup person deleted successfully");
} else {
    header("Location: manage_pickups.php?error=Failed to delete pickup person");
}

$stmt->close();
$deleteStmt->close();
$conn->close();
?>