<?php

header('Content-Type: application/json');

// Optional: Allow CORS for testing
header('Access-Control-Allow-Origin: *');

$specific_data = [];
if (isset($conn)) {
    $query = 'Select * From specific_data';
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $specific_data[] = $row;
        }
    } else {
        $specific_data[] = "0 results";
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

echo json_encode($specific_data);
