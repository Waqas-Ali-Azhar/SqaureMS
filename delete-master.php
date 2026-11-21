<?php
require_once __DIR__ . '/config/database.php';
include 'includes/lib/main_lib.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $delete = "DELETE FROM master_data WHERE id = $id";
    mysqli_query($conn, $delete);

}

header('Location: master.php?deleted=1');
exit();
?>
