<?php

header('Content-Type: application/json');

// Optional: Allow CORS for testing

header('Access-Control-Allow-Origin: *');

$general_name = $_GET['general'] ?? null;

if (!$general_name || !is_string($general_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing project master']);
    exit;
}

$general = [];
if (isset($conn)) {
    $query = " SELECT
    *
FROM
    master_sheet 
WHERE
    general = '$general_name'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $value) {
                if (strpos($key, 'competitor') === 0) {
                    unset($row[$key]);
                }
            }
            $general[] = $row;
        }
    } else {
        $general[] = "0 results";
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

echo json_encode($general);
