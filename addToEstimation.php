<?php
require_once __DIR__ . '/config/database.php';

if (isset($_POST['full_row_data'])) {
    $data = json_decode($_POST['full_row_data'], true);

    // Extract data
    $developers = isset($data['developers']) ? $data['developers'] : [];
    $businessManagers = isset($data['business_managers']) ? $data['business_managers'] : [];
    $developerNames = implode(', ', array_column($developers, 'name'));
    $businessManagerNames = implode(', ', array_column($businessManagers, 'name'));

    $lead_id = intval($data['id']);
    $client_id = intval($data['client_id']);
    $shortDescription = mysqli_real_escape_string($conn, $data['short_description']);

    // Check if estimation exists
    $checkQuery = "SELECT id FROM estimation WHERE lead_id = $lead_id AND client_id = $client_id";
    $result = mysqli_query($conn, $checkQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        // Estimation exists - perform UPDATE (exclude lead_id and client_id)
        $updateQuery = "
            UPDATE estimation 
            SET 
                estimation_title = '$shortDescription',
                developer_name = '$developerNames',
                business_manager_name = '$businessManagerNames'
            WHERE lead_id = $lead_id AND client_id = $client_id
        ";
        if (mysqli_query($conn, $updateQuery)) {
            echo "Estimation updated successfully.";
        } else {
            echo "Update failed: " . mysqli_error($conn);
        }
    } else {
        // No record found - INSERT
        $insertQuery = "
            INSERT INTO estimation 
            (lead_id, client_id, estimation_title, developer_name, business_manager_name)
            VALUES 
            ($lead_id, $client_id, '$shortDescription', '$developerNames', '$businessManagerNames')
        ";
        if (mysqli_query($conn, $insertQuery)) {
            echo "Estimation inserted successfully.";
        } else {
            echo "Insert failed: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);

    // Optional logging
    error_log("Estimation Data: " . print_r($data, true));
} else {
    error_log("No 'full_row_data' in POST");
}
