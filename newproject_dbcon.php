<?php
require_once __DIR__ . '/config/database.php';
include_once __DIR__ . '/includes/lib/main_lib.php';


$query = "SELECT DISTINCT
   mc.id,
    m.Name AS master_name,
    g.name AS general_name,
    s.name AS specific_name
FROM mgs_combined mc
JOIN master_data m ON mc.master_id = m.id
JOIN general_data g ON mc.general_id = g.id
JOIN specific_data s ON mc.specific_id = s.id;
";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST)) {
    $name = $_POST['name'];
    $category = $_POST['nameSelect'];
    $url = $_POST['url'];
    $url_text = $_POST['url-text'];
    $thumbnail1 = $_FILES['thumbnail1'];
    $thumbnail2 = $_FILES['thumbnail2'];

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        echo 'error in finding imgdir';
    }
    $imageName = $_FILES['thumbnail1']['name'];
    $imageTmp = $_FILES['thumbnail1']['tmp_name'];
    $imagePath = $upload_dir . $imageName;
    $thumbName = $_FILES['thumbnail2']['name'];
    $thumbTmp = $_FILES['thumbnail2']['tmp_name'];
    $thumbPath = $upload_dir . $thumbName;
    move_uploaded_file($thumbnail1['tmp_name'], $imagePath);
    move_uploaded_file($thumbnail2['tmp_name'], $thumbPath);
    $insert = "INSERT INTO new_projects (name, category, url, url_text, image, thumbnail)
               VALUES ('$name', '$category', '$url', '$url_text', '$imagePath', '$thumbPath')";
    if (mysqli_query($conn, $insert)) {
        echo "<script>alert('Data inserted successfully');
        window.location.href='add-newproject.php';
        </script>";
    } else {
        echo "Insert error: " . mysqli_error($conn);
    }
}

$result = $conn->query($query);
if (!$result) {
    die("Query Failed:" . $conn->error);
}