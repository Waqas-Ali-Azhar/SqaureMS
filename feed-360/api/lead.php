<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Step 1: Configuration
$tableName = 'lead';
$columns = [
    'name' => 'VARCHAR(255)',
    'whatsapp_number' => 'VARCHAR(20)',
    'city' => 'VARCHAR(100)',
    'lead_status' => 'VARCHAR(50)',
    'lead_source' => 'VARCHAR(100)',
    'demo_id' => 'INT',
    'campaign_referrence' => 'VARCHAR(255)'
];

// Step 2: Capture request data
$method = $_SERVER['REQUEST_METHOD'];
$request = [
    'method' => $method,
    'query' => $_GET,
    'form' => $_POST,
    'body' => json_decode(file_get_contents("php://input"), true),
];
$request['all'] = array_merge(
    $request['query'],
    $request['form'],
    is_array($request['body']) ? $request['body'] : []
);

// Step 3: Connect DB
if (!isset($conn)) {
    require_once('config/db.php');
}

if (!isset($conn)) {
    http_response_code(500);
    echo json_encode(['error' => 'DB Connection Error!']);
    exit();
}

// Step 4: Ensure table and columns exist
$response = [];

$result = $conn->query("SHOW TABLES LIKE '$tableName'");
$tableExists = $result && $result->num_rows > 0;

if (!$tableExists) {
    $columnsSql = [];
    foreach ($columns as $name => $type) {
        $columnsSql[] = "`$name` $type";
    }

    $sql = "CREATE TABLE `$tableName` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        " . implode(", ", $columnsSql) . ",
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    if ($conn->query($sql)) {
        $response['table'] = 'Created';
        foreach (array_keys($columns) as $col) {
            $response['columns'][$col] = 'Created with table';
        }
    } else {
        $response['error'] = 'Table creation failed: ' . $conn->error;
    }
} else {
    $response['table'] = 'Exists';

    $result = $conn->query("DESCRIBE `$tableName`");
    $existingCols = [];
    while ($row = $result->fetch_assoc()) {
        $existingCols[] = $row['Field'];
    }

    foreach ($columns as $col => $type) {
        if (!in_array($col, $existingCols)) {
            $alterSql = "ALTER TABLE `$tableName` ADD `$col` $type";
            if ($conn->query($alterSql)) {
                $response['columns'][$col] = 'Added';
            } else {
                $response['columns'][$col] = 'Error: ' . $conn->error;
            }
        } else {
            $response['columns'][$col] = 'Exists';
        }
    }
}

// Step 5: Insert or Update
$data = $request['all'];
$fieldsToUse = array_keys($columns);

// Only continue if at least one field is present
$validInput = array_intersect_key($data, array_flip($fieldsToUse));
if (empty($validInput)) {
    echo json_encode(['error' => 'No valid input provided']);
    exit();
}

if (isset($data['id']) && is_numeric($data['id'])) {
    $id = intval($data['id']);

    // Check if row exists with this ID
    $checkResult = $conn->query("SELECT id FROM `$tableName` WHERE id = $id LIMIT 1");

    if ($checkResult && $checkResult->num_rows > 0) {
        // --- ID exists → update ---
        $setParts = [];
        foreach ($validInput as $key => $value) {
            $safeVal = $conn->real_escape_string($value);
            $setParts[] = "`$key` = '$safeVal'";
        }

        $sql = "UPDATE `$tableName` SET " . implode(', ', $setParts) . " WHERE id = $id";

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'updated', 'id' => $id]);
        } else {
            echo json_encode(['error' => 'Update failed', 'details' => $conn->error]);
        }

    } else {
        // --- ID does not exist → insert as new ---
        $cols = implode('`, `', array_keys($validInput));
        $vals = implode("', '", array_map([$conn, 'real_escape_string'], array_values($validInput)));

        $sql = "INSERT INTO `$tableName` (`$cols`) VALUES ('$vals')";

        if ($conn->query($sql)) {
            echo json_encode(['status' => 'inserted (id not found, so created new)', 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['error' => 'Insert failed', 'details' => $conn->error]);
        }
    }

} else {
    // --- No ID provided → insert as new ---
    $cols = implode('`, `', array_keys($validInput));
    $vals = implode("', '", array_map([$conn, 'real_escape_string'], array_values($validInput)));

    $sql = "INSERT INTO `$tableName` (`$cols`) VALUES ('$vals')";

    if ($conn->query($sql)) {
        echo json_encode(['status' => 'inserted', 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['error' => 'Insert failed', 'details' => $conn->error]);
    }
}


exit();
