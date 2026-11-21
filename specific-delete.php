<?php
require_once __DIR__ . '/config/database.php';
include_once 'includes/lib/main_lib.php';
verify_user();

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $deleteCombined = "DELETE FROM specific_general_map WHERE specific_id = $id";
    mysqli_query($conn, $deleteCombined);


    $sql = "DELETE FROM specific_data WHERE id = $id";
    mysqli_query($conn, $sql);
}

header('Location: specific.php?deleted=1');
exit();
?>
