<?php
require_once __DIR__ . '/config/database.php';
$data = $_POST;
$parentZero = 0;
$id = !empty($data['taskId']) ? (int)$data['taskId'] : 0;
$milestone_id = $data['milestoneId'];
$taskName = $data['taskName'];
$taskHours    = !empty($data['taskHours']) ? (int)$data['taskHours'] : 0;
$developer = "";
if (!empty($data['developers']) && is_array($data['developers'])) {
    $developer = implode(",", $data['developers']);
}
$businessManager = "";
if (!empty($data['businessManagers']) && is_array($data['businessManagers'])) {
    $businessManager = implode(",", $data['businessManagers']);
}
$tester = "";
if (!empty($data['testers']) && is_array($data['testers'])) {
    $tester = implode(",", $data['testers']);
}
$checkData = "SELECT 1 FROM tasks WHERE id = $id LIMIT 1";
$checkResult = $conn->query($checkData);
if ($checkResult && $checkResult->num_rows > 0) {
    if ($taskName == '') {
        $update = " DELETE FROM tasks WHERE id = $id";
    } else {
        $update = "UPDATE tasks 
               SET milestone_id = $milestone_id,
                   name = '$taskName',
                   estimation_hours = $taskHours,
                   developer = '$developer',
                   business_manager = '$businessManager',
                   tester = '$tester'
               WHERE id = $id";
    }

    if ($conn->query($update)) {
        echo "Task updated (ID: $id)";
    } else {
        echo "Update failed: " . $conn->error;
    }
} else {
    $insert = "INSERT INTO tasks (milestone_id, name, estimation_hours, parent_id, developer, business_manager, tester) 
               VALUES ($milestone_id, '$taskName', $taskHours, $parentZero, '$developer', '$businessManager', '$tester')";
    if ($conn->query($insert)) {
        $newId = $conn->insert_id;
        echo "Task created (ID: $newId)";
    } else {
        echo "Insert failed: " . $conn->error;
    }
}
