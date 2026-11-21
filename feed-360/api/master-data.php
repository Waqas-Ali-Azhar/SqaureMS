<?php

header('Content-Type: application/json');

// Optional: Allow CORS for testing
header('Access-Control-Allow-Origin: *');

$master_data = [];
if (isset($conn)) {
    // $query = 'Select * From master_sheet';
    $query = "
        SELECT m.*
        FROM master_sheet m
        INNER JOIN new_columns n ON m.article_number = n.article_number
        WHERE n.public_view = 'Yes'
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $master_data[] = $row;
        }
    } else {
        $master_data[] = "0 results";
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

// Handle UTF-8 conversion if needed
function utf8ize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = utf8ize($value);
        }
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}

$json = json_encode(utf8ize($master_data));
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['error' => json_last_error_msg()]);
    exit();
}

echo $json;

