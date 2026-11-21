<?php
require_once __DIR__ . '/config/database.php';

$id = $_POST['id'] ?? null;
$column = $_POST['column'] ?? null;
$value = $_POST['value'] ?? null;

if ($id && $column) {
    $sql = "UPDATE estimation SET `$column` = '" . addslashes($value) . "', updated_at = NOW() WHERE id = " . (int)$id;
    if ($conn->query($sql)) {
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
} else {
    echo "Invalid input.";
}
?>
