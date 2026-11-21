<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('./');
// header('Content-Type: application/json');

$estimation_id = (int)($_GET['estimation_id'] ?? 0);

if (!$estimation_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, name, milestone_order, description, estimation_id 
        FROM milestones 
        WHERE estimation_id = $estimation_id
        ORDER BY milestone_order ASC";
$res = mysqli_query($conn, $sql);
$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);
