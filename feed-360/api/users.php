<?php

header('Content-Type: application/json');

// Optional: Allow CORS for testing
header('Access-Control-Allow-Origin: *');

$users = [];
if (isset($conn)) {
    $query = 'Select * From users';
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    } else {
        $users[] = "0 results";
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

echo json_encode($users);
