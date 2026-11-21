<?php

function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false || $value === null || $value === '') {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    if (is_string($value)) {
        $lower = strtolower($value);

        if ($lower === 'true') {
            return true;
        }

        if ($lower === 'false') {
            return false;
        }

        if ($lower === 'null') {
            return null;
        }
    }

    return $value;
}

function loadEnvFile($filePath)
{
    if (!is_file($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);

        $value = preg_replace('/^"(.*)"$/', '$1', $value);
        $value = preg_replace('/^\'(.*)\'$/', '$1', $value);

        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

if (!function_exists('getDbConnection')) {
    function getDbConnection()
    {
        static $conn = null;

        if ($conn !== null) {
            return $conn;
        }

        $envFile = dirname(__DIR__) . '/.env';
        loadEnvFile($envFile);

        $host = env('DB_HOST', 'localhost');
        $username = env('DB_USER', 'root');
        $password = env('DB_PASS', '');
        $database = env('DB_NAME', 'test');

        $conn = new mysqli($host, $username, $password, $database);

        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        $GLOBALS['db_connection'] = $conn;

        return $conn;
    }
}

$conn = getDbConnection();
