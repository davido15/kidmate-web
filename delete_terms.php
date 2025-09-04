<?php
include "session.php";
include 'db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: manage_terms.php?error=Invalid term ID");
    exit;
}

// Check if term exists
$check_query = "SELECT id, term_name FROM terms WHERE id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$term = $check_result->fetch_assoc();

if (!$term) {
    header("Location: manage_terms.php?error=Term not found");
    exit;
}

// Check if term is being used in other tables (optional - add more checks as needed)
// For example, if you have a grades table that references terms
$usage_check_query = "SELECT COUNT(*) as count FROM grades WHERE term_id = ?";
$usage_stmt = $conn->prepare($usage_check_query);
$usage_stmt->bind_param("i", $id);
$usage_stmt->execute();
$usage_result = $usage_stmt->get_result();
$usage_count = $usage_result->fetch_assoc()['count'];

if ($usage_count > 0) {
    header("Location: manage_terms.php?error=Cannot delete term. It is being used in " . $usage_count . " grade record(s)");
    exit;
}

// Delete the term
$delete_query = "DELETE FROM terms WHERE id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    header("Location: manage_terms.php?success=Term '" . htmlspecialchars($term['term_name']) . "' deleted successfully!");
} else {
    header("Location: manage_terms.php?error=Failed to delete term: " . $delete_stmt->error);
}

$delete_stmt->close();
$check_stmt->close();
$usage_stmt->close();
$conn->close();
?> 