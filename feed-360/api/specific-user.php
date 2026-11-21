<?php

header('Content-Type: application/json');

// Optional: Allow CORS for testing

header('Access-Control-Allow-Origin: *');

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing user ID']);
    exit;
}

$user = [];
if (isset($conn)) {
    $query = 'Select * From users where id = '.$id;
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
         while ($row = $result->fetch_assoc()) {
            $user[] = $row;
        }
    } else {
        $user[] = "0 results";
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

echo json_encode($user);
