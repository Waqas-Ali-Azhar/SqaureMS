<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/database.php';

if (!isset($conn) || !$conn) {
    $conn = $GLOBALS['db_connection'] ?? getDbConnection();
}

$basePath = $_SERVER['HTTP_HOST'] ?? 'localhost';

