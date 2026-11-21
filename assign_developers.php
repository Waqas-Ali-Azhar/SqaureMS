<?php
// include_once 'includes/header.php';
require_once __DIR__ . '/config/database.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Karachi');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('debug_log.txt', print_r($_POST, true), FILE_APPEND);

    
    $lead_id = intval($_POST['lead_id']);
    $developer_id = intval($_POST['developer_id']);
    $developer_name = $conn->real_escape_string($_POST['developer_name']);

    if ($lead_id && $developer_id && $developer_name) {
        // Check for existing assignment
        $checkQuery = "
            SELECT 1 FROM assigned_to_developer
            WHERE lead_id = $lead_id AND developer_id = $developer_id
            LIMIT 1
        ";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult && $checkResult->num_rows === 0) {
            // Only insert if not already assigned
            $insertQuery = "
                INSERT INTO assigned_to_developer (lead_id, developer_id, developer_name)
                VALUES ($lead_id, $developer_id, '$developer_name')
            ";
            if ($conn->query($insertQuery)) {
                $time = date('Y-m-d H:i:s');
                  $updateQuery = "
                    UPDATE leads 
                    SET last_updated = '$time', lead_assigned_date = '$time'
                    WHERE id = $lead_id
                ";
                $conn->query($updateQuery);
                echo "success";
            } else {
                echo "Insert error: " . $conn->error;
            }
        } else {
            echo "already exists";
        }
    } else {
        echo "missing data";
    }
}
?>
