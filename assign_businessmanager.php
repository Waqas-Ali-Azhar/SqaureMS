<?php
// include_once 'includes/header.php';
require_once __DIR__ . '/config/database.php';
date_default_timezone_set('Asia/Karachi');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lead_id = intval($_POST['lead_id']);
    $business_manager_id = intval($_POST['business_manager_id']);
    $manager_name = $conn->real_escape_string($_POST['manager_name']);

    if ($lead_id && $business_manager_id && $manager_name) {
        // Check for existing assignment
        $checkQuery = "
            SELECT 1 FROM assigned_to_business_manager
            WHERE lead_id = $lead_id AND business_manager_id = $business_manager_id
            LIMIT 1
        ";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult && $checkResult->num_rows === 0) {
            // Not assigned yet, insert it
            $insertQuery = "
                INSERT INTO assigned_to_business_manager (lead_id, business_manager_id, business_manager_name)
                VALUES ($lead_id, $business_manager_id, '$manager_name')
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
