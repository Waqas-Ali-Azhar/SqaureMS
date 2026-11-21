<?php
require_once __DIR__ . '/config/database.php';
include_once 'includes/header.php';
include_once 'includes/topbar.php';
include_once 'includes/sidebar.php';

$project_id = '';
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $project_id = $_GET['id'];
} else {
    return;
}

$sql = "SELECT * FROM new_projects WHERE id = $project_id";
$result = $conn->query($sql);
$iframe_urls = array();

while ($row = mysqli_fetch_assoc($result)) {


    foreach ($row as $key => $val) {
        if ($key == 'live_link' || strpos($key, "competitor_") !== false) {
            if( !empty($val) && strpos($val, 'https') !== 0){
                $val = 'https://'.$val;
            }
            $iframe_urls[] = $val;
        }
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="content-wrapper">
                <?php
                foreach ($iframe_urls as $key => $val) {
                    echo '<iframe src="' . $val . '" title="description" style="display:block; width:100%; min-height:1000px; margin:0; border:2px solid #343a40; border-radius:10px;"></iframe>';
                    echo '<div style ="width:100%; padding:5px 0; background-color:blue; border-radius:40px;"></div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php

include('includes/footer.php');
