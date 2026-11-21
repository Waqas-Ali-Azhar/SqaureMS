<?php

// require_once __DIR__ . '/config/database.php';
// header('Content-Type: application/json');

// $data = json_decode(file_get_contents('php://input'), true);
// // echo '<pre>';
// // print_r($data);
// // echo '</pre>';
// // exit();

// $rowId = intval($data['row_id'] ?? 0);
// $column = $data['column'] ?? '';
// $value = $data['value'] ?? '';
// // $plugin_view = $data['public_view'] ?? '';
// // $plugin_installed = $data['plugin_installed'] ?? '';

// // Basic validation
// if (!$rowId || !$column) {
//     echo json_encode(['success' => false, 'message' => 'Invalid input']);
//     exit;
// }

// // Sanitize value
// $escapedValue = mysqli_real_escape_string($conn, $value);
// if ($column === 'public_view' || $column === 'plugin_installed') {
//     $updateQuery = "UPDATE master_sheet SET `$column` = '$escapedValue' WHERE article_number = '$rowId'";
//     $updateQuery2 = "UPDATE new_columns SET `$column` = '$escapedValue' WHERE article_number = '$rowId'";
// } else {
//     // Update `cache_status` table (adjust if you want others)
//     $updateQuery = "UPDATE cache_status SET `$column` = '$escapedValue' WHERE article_number = '$rowId'";
// }
// if (mysqli_query($conn, $updateQuery)) {
//     echo json_encode(['success' => true]);
// } else {
//     echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
// }
// if (mysqli_query($conn, $updateQuery)) {
//     echo json_encode(['success' => true]);
// } else {
//     echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
// }
 $newcolumnsArray = ["public_view", "returns", "plugin_installed", "multi_demos", "multi_category", "custom_coded", "variation_product", "service_forms"];

require_once __DIR__ . '/config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
// echo '<pre>';
// print_r($data);
// echo '</pre>';
// exit();

$rowId = intval($data['row_id'] ?? 0);
$column = $data['column'] ?? '';
$value = $data['value'] ?? '';
// $plugin_view = $data['public_view'] ?? '';
// $plugin_installed = $data['plugin_installed'] ?? '';


// Basic validation
if (!$rowId || !$column) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Sanitize value
$escapedValue = mysqli_real_escape_string($conn, $value);

$success = false;  // Flag to track overall success

if (in_array($column, $newcolumnsArray)) {
    // Update both master_sheet and new_columns tables
    $updateQuery = "UPDATE master_sheet SET `$column` = '$escapedValue' WHERE article_number = '$rowId'";
    $updateQuery2 = "UPDATE new_columns SET `$column` = '$escapedValue' WHERE article_number = '$rowId'";

    if (mysqli_query($conn, $updateQuery)) {
        // If the first update is successful, proceed to the second one
        if (mysqli_query($conn, $updateQuery2)) {
            $success = true;
        } else {
            // If the second update fails, return error
            echo json_encode(['success' => false, 'message' => 'Error updating new_columns: ' . mysqli_error($conn)]);
            exit;
        }
    } else {
        // If the first update fails, return error
        echo json_encode(['success' => false, 'message' => 'Error updating master_sheet: ' . mysqli_error($conn)]);
        exit;
    }
} elseif ($column === 'ssl_status') {
    // Update both master_sheet and new_columns tables
    $updateQuery = "UPDATE ssl_status SET `$column` = '$escapedValue' WHERE article_number = '$rowId'";
    // $updateQuery2 = "UPDATE ssl_status SET `$column` = '$escapedValue' WHERE article_number = '$rowId'";

    if (mysqli_query($conn, $updateQuery)) {
        // If the first update is successful, proceed to the second one
        if (mysqli_query($conn, $updateQuery2)) {
            $success = true;
        } else {
            // If the second update fails, return error
            echo json_encode(['success' => false, 'message' => 'Error updating new_columns: ' . mysqli_error($conn)]);
            exit;
        }
    } else {
        // If the first update fails, return error
        echo json_encode(['success' => false, 'message' => 'Error updating master_sheet: ' . mysqli_error($conn)]);
        exit;
    }
} else {
    // Update `cache_status` table (adjust if you want others)
    $updateQuery = "UPDATE cache_status SET `$column` = '$escapedValue' WHERE article_number = '$rowId'";

    if (mysqli_query($conn, $updateQuery)) {
        $success = true;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating cache_status: ' . mysqli_error($conn)]);
        exit;
    }
}

if ($success) {
    echo json_encode(['success' => true]);
}
