<?php

header('Content-Type: application/json');

// Optional: Allow CORS for testing
header('Access-Control-Allow-Origin: *');

$projects = [];
if (isset($conn)) {
    $query = 'Select * From new_projects';
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
    } else {
        $projects[] = "0 results";
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

echo json_encode($projects);
