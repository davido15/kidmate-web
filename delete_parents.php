<?php
include 'db.php';
include 'session.php';

// Get parent ID from query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: manage_parents.php?error=Invalid parent ID");
    exit;
}

// Check if parent exists
$stmt = $conn->prepare("SELECT id, name FROM parents WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$parent = $result->fetch_assoc();

if (!$parent) {
    header("Location: manage_parents.php?error=Parent record not found");
    exit;
}

// Check if parent has any kids
$kidsStmt = $conn->prepare("SELECT COUNT(*) as count FROM kids WHERE parent_id = ?");
$kidsStmt->bind_param("i", $id);
$kidsStmt->execute();
$kidsResult = $kidsStmt->get_result();
$kidsCount = $kidsResult->fetch_assoc()['count'];

if ($kidsCount > 0) {
    header("Location: manage_parents.php?error=Cannot delete parent. This parent has " . $kidsCount . " child(ren) assigned. Please reassign or delete the children first.");
    exit;
}

// Delete the parent
$deleteStmt = $conn->prepare("DELETE FROM parents WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    header("Location: manage_parents.php?success=Parent deleted successfully");
} else {
    header("Location: manage_parents.php?error=Failed to delete parent");
}

$stmt->close();
$kidsStmt->close();
$deleteStmt->close();
$conn->close();
?>