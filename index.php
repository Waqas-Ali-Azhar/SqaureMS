<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  // header("Location: form.php"); 
  // exit;
}

if (!empty($_GET) && !empty($_GET['logout'])) {
  session_destroy($_SESSION['auth']);
  exit;
}

// echo '<pre>';
// print_r($_REQUEST);
// echo '</pre>';
// exit();

?>

<?php

// print_r(include 'includes/sidebar.php');
// exit;
require_once __DIR__ . '/config/database.php';
include_once __DIR__ . '/includes/header.php';
include_once __DIR__ . '/includes/topbar.php';
include_once __DIR__ . '/includes/sidebar.php';

?>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <!-- <h1 class="m-0 text-dark">Dashboard</h1> -->
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <!-- <li class="breadcrumb-item"><a href="#">Home</a></li> -->
            <!-- <li class="breadcrumb-item active">Dashboard v1</li> -->
          </ol>

        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->
  <?php
  // $type = $_GET['type'] ?? null;
  // $slug = $_GET['slug'] ?? null;
  // // if ($type !== 'specific' || empty($slug)) {
  // //   http_response_code(404);
  // //   echo "Invalid request.";
  // //   // exit;
  // // }
  // $sql = "SELECT * FROM specific_data WHERE slug = '$slug'";
  // $result = mysqli_query($conn, $sql);
  // $specific_name = '';
  // if (mysqli_num_rows($result) > 0) {
  //   $row = mysqli_fetch_assoc($result);
  //   $specific_name = $row['name'];

  //   echo "<table border='1' cellpadding='8' cellspacing='0'>";

  //   echo "<thead><tr>";
  //   foreach ($row as $key => $value) {
  //     echo "<th>" . htmlspecialchars($key) . "</th>";
  //   }
  //   echo "</tr></thead>";
  //   echo "<tbody><tr>";
  //   foreach ($row as $key => $value) {
  //     echo "<td>" . $value . "</td>";
  //   }
  //   echo "</tr></tbody>";

  //   echo "</table>";
  // }
  // $member_sql = "SELECT * FROM master_sheet WHERE specific_data = '$specific_name'";
  // $member_result = mysqli_query($conn, $member_sql);
  // if (mysqli_num_rows($member_result) > 0) {
  //   $member_row = mysqli_fetch_assoc($member_result);
  //   $_SESSION['member_data'] = [
  //     'demolink' => $member_row['demo_link'],
  //     'screenshotlink' => $member_row['screenshot_link'],
  //     'livelink' => $member_row['live_link'],
  //     'themedownloadlink' => $member_row['theme_download_link']
  //   ];
  //   // header("Location: content.php");
  //   // exit;
  // } 
  // else {
  //   echo "No entry found for slug '$slug'";
  // }
  //---------------

  ?>
</div>

<?php
include('includes/footer.php');
?>