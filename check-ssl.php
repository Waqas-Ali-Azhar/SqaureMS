<?php
// DB Configuration
// $host = 'localhost';
// $dbname = 'portfolio-admin';
// $user = 'root';
// $pass = '';

require_once __DIR__ . '/config/database.php';
$table = 'master_sheet';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all websites
    $stmt = $pdo->query("SELECT id, live_link FROM $table");
    $websites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($websites as $site) {
        $url = $site['live_link'];
        $id = $site['id'];

        $hasSSL = checkSSL($url);
        $sslStatus = $hasSSL ? 'yes' : 'no';

        // Update the ssl column
        $updateStmt = $pdo->prepare("UPDATE $table SET ssl_installed = :ssl WHERE id = :id");
        $updateStmt->execute(['ssl' => $sslStatus, 'id' => $id]);

        echo "Checked $url - SSL: $sslStatus\n <br>";
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}


// Function to check SSL
function checkSSL($domain)
{
    $port = 443;
    $errno = 0;
    $errstr = '';
    $timeout = 10;

    // Ensure domain doesn't have protocol
    $domain = preg_replace('#^https?://#', '', rtrim($domain, '/'));

    $connection = @stream_socket_client(
        "ssl://$domain:$port",
        $errno,
        $errstr,
        $timeout,
        STREAM_CLIENT_CONNECT,
        stream_context_create(['ssl' => ['capture_peer_cert' => true]])
    );

    return $connection !== false;
}
