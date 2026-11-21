<?php
// print_r(include 'includes/sidebar.php');
// exit;
require_once __DIR__ . '/config/database.php';
include_once 'includes/header.php';
include_once 'includes/topbar.php';
include_once 'includes/sidebar.php';
$uri = $_SERVER['REQUEST_URI'];
$slug = basename(parse_url($uri, PHP_URL_PATH));

$sql = " SELECT
    sd.id AS sd_id,
    sd.slug AS sd_slug,
    sp.specific_id AS sp_project_id,
    np.*
FROM
    specific_data AS sd
INNER JOIN specific_project AS sp
    ON sd.id = sp.specific_id
INNER JOIN new_projects AS np
    ON sp.project_id = np.id
WHERE
    slug = '$slug'";
$result = $conn->query($sql);
//-------------------

$preferred_columns = ['article_number', 'live_link', 'username', 'password', 'status'];

//----------------------
$project_id = '';

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="content-wrapper">
                <div class="table-wrapper specific-wrapper">
                    <div class="table-space ">
                        <div class="table-scroll-wrapper">
                            <?php
                            $table = '';
                            $firstRow = true;
                            $table .= "<table style='border-collapse: collapse; margin-bottom:5px;' class='filter-table filter-specific'>";
                            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                                || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

                            $host = $_SERVER['HTTP_HOST']; 
                            $baseUrl = $protocol . $host;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $project_id = $row['id'];
                                $id = $row['id'];

                                if ($firstRow) {
                                    // Determine column order
                                    $all_columns = array_keys($row);
                                    $remaining_columns = array_diff($all_columns, $preferred_columns);
                                    $final_column_order = array_merge($preferred_columns, $remaining_columns);

                                    // Header
                                    $table .= '<thead><tr>';
                                    foreach ($final_column_order as $key) {
                                        $table .= '<th>' . $key . '</th>';
                                    }
                                    $table .= '<th><p style="margin:0;">target view</p></th>';
                                    $table .= '</tr></thead>';
                                    $table .= '<tbody>';
                                    $firstRow = false;
                                }

                                $table .= '<tr>';
                                foreach ($final_column_order as $key) {
                                    $value = $row[$key] ?? '';
                                    if (!empty($value)) {
                                        if (filter_var($value, FILTER_VALIDATE_URL) || preg_match('#^https?://#', $value)) {
                                            $table .= '<td><a href="' . $value . '" target="_blank">' . $value . '</a></td>';
                                        } else {
                                            $table .= '<td>' . $value . '</td>';
                                        }
                                    } else {
                                        $table .= '<td>' . $value . '</td>';
                                    }
                                }

                                // $table .= '<td><a href="http://abc.test/specific/' . $slug . '/content/' . $project_id . '" target="_blank">target</a> </td>';
                                $table .= '<td><a href="' . $baseUrl . '/specific/' . $slug . '/content/' . $project_id . '" target="_blank">target</a></td>';
                                $table .='<td><a href="/edit_projects.php?id='.$id.'">edit</a></td>';
                                $table .= '</tr>';
                            }
                            $table .= '</tbody></table>';
                            echo $table;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include 'content.php';
?>
<!-- horizontal scroll table -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollWrapper = document.querySelector('.table-scroll-wrapper');

        if (scrollWrapper) {
            scrollWrapper.addEventListener('wheel', function(e) {
                if (scrollWrapper.scrollWidth > scrollWrapper.clientWidth) {
                    e.preventDefault();
                    scrollWrapper.scrollLeft += e.deltaY;
                }
            }, {
                passive: false
            });
        }
    });
</script>
<?php
include('includes/footer.php');
?>