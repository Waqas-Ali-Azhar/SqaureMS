<?php

header('Content-Type: application/json');

// Optional: Allow CORS for testing

header('Access-Control-Allow-Origin: *');

$slug = $_GET['slug'] ?? null;

if (!$slug || !is_string($slug)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing project slug']);
    exit;
}

$project = [];
if (isset($conn)) {
    $query = "SELECT
    np.*
FROM
    specific_data AS sd
INNER JOIN specific_project AS sp
    ON sd.id = sp.specific_id
INNER JOIN new_projects AS np
    ON sp.project_id = np.id
WHERE
    slug = '$slug'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $project[] = $row;
        }
    } else {
        $project[] = "0 results";
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

echo json_encode($project);
