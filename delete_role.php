<?php
require_once __DIR__ . '/config/database.php'; // Ensure DB connection
// Optionally include header/topbar/sidebar if needed

// Check if ID is present and numeric
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid Role ID'); window.location.href='manage_roles.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// You may want to check if the role exists before deleting
$sql = "SELECT * FROM roles WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<script>alert('Role not found'); window.location.href='manage_roles.php';</script>";
    exit;
}

// Delete the role
$deleteSql = "DELETE FROM roles WHERE id = $id";
if ($conn->query($deleteSql)) {
    echo "<script>alert('Role deleted successfully.'); window.location.href='manage_roles.php';</script>";
}
?>
