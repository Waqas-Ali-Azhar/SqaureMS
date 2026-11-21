<?php
include_once 'includes/header.php';
include 'includes/topbar.php';
include 'includes/sidebar.php';
$filepath = '';
$filepath = str_replace('/','',$_SERVER['REQUEST_URI']);
$whereClauses = [];
foreach ($_POST as $key => $values) {
    if ($key === 'row_ids') {
        continue;
    }
    if (is_array($values) && !empty($values)) {
        $escaped = array_map(function ($val) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $val) . "'";
        }, $values);
        $whereClauses[] = "$key IN (" . implode(',', $escaped) . ")";
    }
}
$whereSQL = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : ''; // for filters data show
$sql = "
SELECT new_projects.*, project_logos.thumbnail 
FROM new_projects 
LEFT JOIN project_logos ON new_projects.id = project_logos.project_id 
$whereSQL
";
$result = $conn->query($sql);
$columnsData = [];
$dataRows = [];

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="content-wrapper">
                <div class="table-wrapper">
                    <div class="table-space ">
                        <div class="table-scroll-wrapper">
                            <?php
                            $rowId = '';
                            $table = '';
                            $firstRow = true;
                            $table .= "<form id='data-form' name='data-form' method='POST' class='form-filter'>";
                            //----
                            //div for display of selected value in tab
                            $table .= "<div class='selected-tags' style='display:flex;'>";
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                                foreach ($_POST as $key => $values) {
                                    if ($key === 'row_ids') {
                                        continue;
                                    }
                                    if (is_array($values)) {
                                        $all_value = implode(',', $values);

                                        $table .= "<div style=' color:black; font-weight:700;  margin-right:5px;' class='selected-tag' id='' data-value='{$all_value}' data-select-id='{$key}'>";

                                        $table .= "{$key} <span class='remove-tag' style='cursor:pointer; margin-left:4px;'><b>&times;</b></span>";
                                        $table .= "</div>";
                                        foreach ($values as $value) {
                                            $chckValue = htmlspecialchars($value);
                                            $tagId = "{$key}_tag_{$chckValue}";

                                            $table .= "<div style='border:1px solid #cdcbcb; border-radius:5px; margin-right:5px;' class='selected-tag' id='{$tagId}' data-value='{$chckValue}' data-select-id='{$key}'>";
                                            $table .= "{$chckValue} <span class='remove-tag' style='cursor:pointer; margin-left:4px;'>&times;</span>";
                                            $table .= "</div>";
                                        }
                                    }
                                }
                            }
                            $table .= "</div>";
                            //----
                            //--- Check if any filter was applied
                            $hasFilters = false;
                            foreach ($_POST as $key => $values) {
                                if ($key !== 'row_ids' && is_array($values) && !empty($values)) {
                                    $hasFilters = true;
                                    break;
                                }
                            }
                            $table .= "<input type='hidden' id='filters-applied' value='" . ($hasFilters ? '1' : '0') . "'>";
                            //----end check-------
                            $table .= "<table style='border-collapse: collapse;' class='filter-table table-projects'>";
                            while ($row = mysqli_fetch_assoc($result)) {
                                if ($firstRow) {
                                    $table .= '<thead><tr>';
                                    // $table .= '<th> action</th>';
                                    foreach ($row as $key => $value) {
                                        if ($key == 'id') continue;
                                        if ($key =='ssl_installed'){
                                            $key = strtoupper($key);
                                        }
                                        $table .= '<th>' . ucfirst($key) . '</th>';
                                    }
                                    $table .= '<th> action</th>';
                                    $table .= '</tr>';

                                    $firstRow = false;
                                    //-------------------------------
                                    $table .= "<tr class='dd-row'>";
                                    foreach ($row as $key => $value) {
                                        if ($key == 'id' || $key == 'thumbnail') continue;

                                        $table .= "<td style='border:1px solid;'>";

                                        $table .= "<select multiple name='{$key}[]' data-id='$key' class='multiselect' id='{$key}'>";

                                        // Fetch distinct values from the DB
                                        $distinctSql = "SELECT DISTINCT `$key` FROM new_projects WHERE `$key` IS NOT NULL AND `$key` != ''";
                                        $distinctResult = $conn->query($distinctSql);

                                        if ($distinctResult) {
                                            while ($optRow = $distinctResult->fetch_assoc()) {
                                                $option = htmlspecialchars($optRow[$key], ENT_QUOTES, 'UTF-8');
                                                $selected = (isset($_POST[$key]) && in_array($optRow[$key], (array)$_POST[$key])) ? "selected" : "";
                                                $table .= "<option value=\"{$option}\" {$selected}>{$option}</option>";
                                            }
                                        }

                                        $table .= "</select>";
                                        $table .= "</td>";
                                    }

                                    $table .= "</tr>";
                                    $table .= '</thead><tbody>';
                                    //-----------------
                                }
                                $table .= '<tr>';
                                foreach ($row as $key => $value) {
                                    $rowId = $row['id'];
                                    //for thumbnail-----------------
                                    if ($key === 'thumbnail') {
                                        if (!empty($value)) {
                                            // $baseUrl = 'http://abc.test/'; 
                                            // $thumbnailPath = ltrim($value, '/');
                                            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                                            $host = $_SERVER['HTTP_HOST'];
                                            $baseUrl = $protocol . $host . '/'; // dynamic base URL
                                            $thumbnailPath = ltrim($value, '/'); // remove leading slash if any
                                            $fullThumbnailUrl = $baseUrl . $thumbnailPath;
                                            $table .= '<td><a href="' . $fullThumbnailUrl . '" target="_blank">
                                                         <img src="' . $fullThumbnailUrl . '" alt="Logo" style="height:40px;">
                                                         </a></td>';
                                        } else {
                                            $table .= '<td>No Logo</td>';
                                        }
                                        continue;
                                    }

                                    if ($key == 'id') continue;
                                    if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                                        $table .= '<td><a href="' . htmlspecialchars($value) . '" target="_blank">' . htmlspecialchars($value) . '</a></td>';
                                    } elseif (is_string($value) && preg_match('/\b(?:https?:\/\/)?[a-z0-9\-]+(?:\.[a-z0-9\-]+)+(?:\/[^\s]*)?\b/i', $value)) {
                                        $url = preg_match('#^https?://#i', $value) ? $value : 'http://' . $value;
                                        $table .= '<td><a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($value) . '</a></td>';
                                    } else {
                                        $table .= '<td>' . htmlspecialchars((string)$value) . '</td>';
                                    }


                                    // $table .= '<td>' . $value . '</td>';
                                }
                                //for edit row store id into hidden input
                                $table .= "<td style='border:1px solid;'>
                                 <input type='hidden' name='row_ids[]' value='{$rowId}'>
                                 <a href='edit_projects.php?id={$rowId}'><i class='far fa-edit'></i></a>
                                 </td>";
                                $table .= '</tr>';
                            }
                            $table .= '</tbody></table>';
                            $table .= '</form>';
                            echo $table;

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- horizontal scroll table -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollWrapper = document.querySelector('.table-scroll-wrapper');
        const tbody = scrollWrapper?.querySelector('tbody');
        const tableWrapper = document.querySelector('.table-wrapper');
        // Horizontal scroll on table ONLY
        if (scrollWrapper && tbody) {
            tbody.addEventListener('wheel', function(e) {
                const canScroll = scrollWrapper.scrollWidth > scrollWrapper.clientWidth;
                if (canScroll) {
                    e.preventDefault();
                    scrollWrapper.scrollLeft += e.deltaY;
                    e.stopPropagation();
                }
            }, {
                passive: false
            });
        }

        if (tableWrapper) {
            tableWrapper.addEventListener('wheel', function(e) {
                //&& !e.target.parentElement.parentElement.classList.contains('ulList')
                if (scrollWrapper.scrollHeight > scrollWrapper.clientHeight && !(e.target.closest('.ulList'))) {
                    e.preventDefault();
                    scrollWrapper.scrollTop += e.deltaY;
                }
            }, {
                passive: false
            });
        }
    });
</script>
<?php include('includes/footer.php'); ?>