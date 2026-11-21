<?php
require_once('./');
header('Content-Type: application/json');

$id            = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name          = $_POST['name'] ?? '';
$order         = (int)($_POST['order'] ?? 0);
$description   = $_POST['description'] ?? '';
$estimation_id = (int)($_POST['estimation_id']);

if (!$estimation_id) {
    echo json_encode(['success' => false, 'error' => 'Missing estimation_id']);
    exit;
}

// Check if milestone exists (by id + estimation_id)
$exists = false;
if ($id > 0) {
    $check = mysqli_query($conn, "SELECT id FROM milestones 
                                   WHERE id = $id AND estimation_id = $estimation_id");
    $exists = mysqli_num_rows($check) > 0;
}

if ($exists) {
    // Update existing
    $sql = "UPDATE milestones 
            SET name = '$name', description = '$description'
            WHERE id = $id AND estimation_id = $estimation_id";
    $ok = mysqli_query($conn, $sql);
    echo json_encode(['success' => (bool)$ok, 'id' => $id]);
} else {
    // Insert new
    $sql = "INSERT INTO milestones (name, description, milestone_order, estimation_id)
            VALUES ('$name', '$description', $order, $estimation_id)";
    $ok = mysqli_query($conn, $sql);
    $newId = mysqli_insert_id($conn);
    echo json_encode(['success' => (bool)$ok, 'id' => $newId]);
}
