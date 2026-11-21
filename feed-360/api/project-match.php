<?php

header('Content-Type: application/json');

// Optional: Allow CORS for testing

header('Access-Control-Allow-Origin: *');

$master = $_GET['master'] ?? null;
$general = $_GET['general'] ?? null;
$specific = $_GET['specific'] ?? null;

if (!$master || !is_string($master) || !$general || !is_string($general) || !$specific || !is_string($specific)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing paramerter', 'valid_url' => '/master/{slug}/general/{slug}/specific/{slug}']);
    exit;
}

$project_match = [];
if (isset($conn)) {
    $query = " SELECT
    *
FROM
    master_sheet 
WHERE
    master = '$master'
    AND
    general = '$general'
    AND
    specifics = '$specific'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $value) {
                if (strpos($key, 'competitor') === 0) {
                    unset($row[$key]);
                }
            }
            $project_match[] = $row;
        }
    } else {
        $project_match[] = "0 results";
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

echo json_encode($project_match);
