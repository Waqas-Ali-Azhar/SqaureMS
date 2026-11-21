<?php

header('Content-Type: application/json');

// Optional: Allow CORS for testing

header('Access-Control-Allow-Origin: *');


$master = [];
if (isset($conn)) {
    $query = " SELECT
    *
FROM
    master_data";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $value) {
                if (strpos($key, 'competitor') === 0) {
                    unset($row[$key]);
                }
            }
            $master[] = $row;
        }
    } else {
        $master[] = "0 results";
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

echo json_encode($master);
