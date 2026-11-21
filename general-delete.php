<?php
require_once __DIR__ . '/config/database.php';
include 'includes/lib/main_lib.php';
verify_user();

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $deleteCombined = "DELETE FROM combined_data WHERE general_id = $id";
    mysqli_query($conn, $deleteCombined);


    $sql = "DELETE FROM general_data WHERE id = $id";
    mysqli_query($conn, $sql);
}

header('Location: general.php?deleted=1');
exit();
?>
