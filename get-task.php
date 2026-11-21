<?php
require_once('./');

$milestone_id = (int)($_GET['milestone_id'] ?? 0);
if (!$milestone_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, name, estimation_hours, milestone_id, developer,business_manager,tester
        FROM tasks
        WHERE milestone_id = $milestone_id";
$res = mysqli_query($conn, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $developers = $row['developer'] ? explode(',', $row['developer']) : [];
    $business_manager = $row['business_manager'] ? explode(',', $row['business_manager']) : [];
    $testers = $row['tester'] ? explode(',', $row['tester']) : [];

    $data[] = [
        "id" => (int)$row['id'],
        "task_name" => $row['name'],                  
        "task_hours" => (int)$row['estimation_hours'],
        "milestone_id" => (int)$row['milestone_id'],
        "developers" => $developers,       
        "business_manager" => $business_manager,
        "testers" => $testers          
    ];
}
echo json_encode($data);
