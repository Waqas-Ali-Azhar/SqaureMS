<style>
    /* Custom Multi-select Container */
    .custom-multiselect-container {
        position: relative;
        display: inline-block;
        width: 100%;
        /* Adjust as needed */
        cursor: pointer;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px;
        box-sizing: border-box;
        background-color: #fff;
        min-height: 34px;
        /* To match input height */
    }

    /* Display for selected items */
    .selected-items-display {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        min-height: 24px;
        /* To ensure space even with no selections */
    }

    /* Individual selected tag */
    .selected-item-tag {
        background-color: #e0e0e0;
        border-radius: 3px;
        padding: 2px 8px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .selected-item-tag .remove-item {
        cursor: pointer;
        font-weight: bold;
        color: #666;
    }

    /* Dropdown list for options */
    .custom-multiselect-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        border: 1px solid #ccc;
        background-color: #fff;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        /* Ensure it's above other elements */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: none;
        /* Hidden by default */
    }

    .custom-multiselect-options.active {
        display: block;
    }

    /* Individual option in the dropdown */
    .custom-multiselect-option {
        padding: 8px 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .custom-multiselect-option label,
    input:hover {
        cursor: pointer;
    }

    .custom-multiselect-option:hover {
        background-color: #f0f0f0;
    }

    /* Checkbox for options */
    .custom-multiselect-option input[type="checkbox"] {
        margin-right: 5px;
    }
</style>
<!-- ............ -->
<?php
include_once('includes/header.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');
?>

<?php
//--------------
$filepath = '';
$filepath = str_replace('/', '', $_SERVER['REQUEST_URI']);
$pluginsquery = "SELECT * FROM plugins";
$pluginResult = $conn->query($pluginsquery);
$pluginOptionsByType = [
    'cache_plugin' => [],
    'optimization_plugin' => [],
    'image_optimization_plugin' => [],
    'cdn_plugin' => [],
];

$map = [
    'cache' => 'cache_plugin',
    'optimization' => 'optimization_plugin',
    'image optimization' => 'image_optimization_plugin',
    'cdn' => 'cdn_plugin',
];

if ($pluginResult && $pluginResult->num_rows > 0) {
    while ($plugin = $pluginResult->fetch_assoc()) {
        // $type = strtolower(trim($plugin['plugin_type']));
        // $name = trim($plugin['plugin_name']);

        // if (isset($map[$type])) {
        //     $pluginOptionsByType[$map[$type]][] = $name;
        // }
        $name = trim($plugin['plugin_name']);
        $types = explode(',', strtolower($plugin['plugin_type']));

        foreach ($types as $type) {
            $type = trim($type); // trim spaces between types
            if (isset($map[$type])) {
                $pluginOptionsByType[$map[$type]][] = $name;
            }
        }
    }
    foreach ($pluginOptionsByType as $key => $list) {
        $pluginOptionsByType[$key] = array_unique($list);
    }
}

//----------------------------//
$totalQuery = "SELECT COUNT(*) as total FROM master_sheet";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalCount = $totalRow['total'];

$columnsWithMultiselect = ['cache_status', 'cache_plugin', 'selected_cache_plugin', 'cache_comments', 'optimization_status', 'optimization_plugin', 'selected_optimization_plugin', 'optimization_comments', 'image_optimization_status', 'image_optimization_plugin', 'selected_image_optimization_plugin', 'image_optimization_comments', 'cdn_status', 'cdn_plugin', 'selected_cdn_plugin', 'cdn_comments', 'cloudflare_status'];

// here is  filtered query
$whereClauses = [];

foreach ($_POST as $key => $values) {
    if ($key === 'row_ids') {
        continue;
    }
    if (is_array($values) && !empty($values)) {
        $escaped = array_map(function ($val) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $val) . "'";
        }, $values);
        $table = in_array($key, $columnsWithMultiselect) ? 'cache_status' : 'master_sheet'; //set table reference for column
        $whereClauses[] = "$table.$key IN (" . implode(',', $escaped) . ")";
    }
}
//------------------------------------------------
$whereSQL = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) : ''; // for filters data show
// $queryselect = mysqli_query($conn, "SELECT * FROM master_sheet $whereSQL");

// $queryselect = "SELECT * FROM master_sheet $whereSQL";
$queryselect = "
SELECT 
    master_sheet.id,
    master_sheet.article_number,
    master_sheet.master,
    master_sheet.general,
    master_sheet.specifics,
    master_sheet.theme_name,
    master_sheet.status,
    master_sheet.demo_link,
    master_sheet.screenshot_link,
    master_sheet.live_link,
    master_sheet.platform,
    master_sheet.submitted_by,
    master_sheet.suggested_name,
    master_sheet.logos_to_be_made,
    master_sheet.theme_download_link,
    master_sheet.token_links,
    master_sheet.test,
    master_sheet.tokens_to_be_used,
    master_sheet.price,
    master_sheet.credentials,
    master_sheet.username,
    master_sheet.password,
    master_sheet.correct_business_name,
    master_sheet.business_name,
    master_sheet.comments,
    master_sheet.installation_by,
    master_sheet.competitor_1,
    master_sheet.competitor_2,
    master_sheet.competitor_3,
    master_sheet.competitor_4,
    master_sheet.competitor_5,
    master_sheet.competitor_6,
    master_sheet.competitor_7,
    master_sheet.competitor_8,
    master_sheet.competitor_9,
    master_sheet.competitor_10,
    master_sheet.allowed_url,
    master_sheet.page_load_speed_w,
    master_sheet.comments_w,
    master_sheet.page_load_speed_s,
    master_sheet.comments_s,
    master_sheet.page_load_speed_h,
    master_sheet.comments_h,
    cache_status.cache_status,
    cache_status.cache_plugin,
    cache_status.cache_comments,
    cache_status.optimization_status,
    cache_status.optimization_plugin,
    cache_status.optimization_comments,
    cache_status.image_optimization_status,
    cache_status.image_optimization_plugin,
    cache_status.image_optimization_comments,
    cache_status.cdn_status,
    cache_status.cdn_plugin,
    cache_status.cdn_comments,
    cache_status.aggregation_status_css,
    cache_status.aggregation_status_js,
    cache_status.cloudflare_status,
    new_columns.public_view,
    new_columns.returns,
    new_columns.plugin_installed,
    new_columns.multi_demos,
    new_columns.multi_category,
    new_columns.custom_coded,
    new_columns.variation_product,
    new_columns.service_forms,
    ssl_status.ssl_status
FROM master_sheet
LEFT JOIN cache_status ON master_sheet.article_number = cache_status.article_number
LEFT JOIN new_columns ON master_sheet.article_number = new_columns.article_number
LEFT JOIN ssl_status ON master_sheet.article_number = ssl_status.article_number

$whereSQL
";
$result = $conn->query($queryselect);
$preferred_columns_raw = ['id', 'article_number', 'master', 'general', 'specifics', 'live_link', 'ssl_status', 'cache_status', 'cache_plugin', 'selected_cache_plugin', 'cache_comments', 'optimization_status', 'optimization_plugin', 'selected_optimization_plugin', 'optimization_comments', 'image_optimization_status', 'image_optimization_plugin', 'selected_image_optimization_plugin', 'image_optimization_comments', 'cdn_status', 'cdn_plugin', 'selected_cdn_plugin', 'cdn_comments', '', 'aggregation_status_css', 'aggregation_status_js', 'page_load_speed_w', 'comments_w', 'page_load_speed_s', 'comments_s', 'page_load_speed_h', 'comments_h', 'public_view', 'plugin_installed', 'username', 'password', 'status'];

$final_column_order = []; // initialize to prevent undefined error

if ($result && $result->num_rows > 0) {
    $all_fields = $result->fetch_fields();
    $all_columns = array_map(fn($f) => $f->name, $all_fields);

    // Only include preferred columns that exist in the actual table
    $preferred_columns = array_values(array_filter($preferred_columns_raw, fn($col) => in_array($col, $all_columns)));

    // Find remaining columns that are not in preferred
    $remaining_columns = array_diff($all_columns, $preferred_columns);

    // Merge preferred + remaining
    $final_column_order = array_merge($preferred_columns, $remaining_columns);
}
// echo '<pre>';
// print_r($final_column_order);
// echo '</pre>';
// exit();
//-------------------------------------------------
?>
<!-- <div class="content-wrapper"> -->

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="content-wrapper">
                <div class="table-wrapper" style="display:flex;">
                    <?php
                    $table = '';
                    $table .= '<div class="table-scroll-wrapper">';
                    $table .= "<form id='data-form' name='data-form' method='POST' class='form-filter'>";
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
                    if ($queryselect) {
                        $all_rows = [];
                        $uniqueValues = [];

                        while ($row = mysqli_fetch_assoc($result)) {
                            $all_rows[] = $row;
                            foreach ($row as $key => $value) {
                                $uniqueValues[$key][$value] = true;
                            }
                        }
                        $count = count($all_rows);
                        if (!empty($final_column_order)) {
                            $table .= "<div class='count-div'><span class='count-span' style='color:rgb(0, 126, 219);'><strong>Currently showing:</strong> {$count} <strong>of</strong> {$totalCount}</span></div>";
                            $table .= "<table style='border-collapse: collapse;' class='filter-table'>";
                            // Table headers
                            $table .= "<thead><tr>";
                            foreach ($final_column_order as $key) {

                                $capitalfirst = str_replace('_', ' ', ucfirst(strtolower($key)));
                                if ($capitalfirst === 'Ssl status') {
                                    $capitalfirst = strtoupper($capitalfirst); //uppercase ssl installed
                                }

                                $table .= "<th data-column-name=\"" . htmlspecialchars($key) . "\">$capitalfirst</th>";
                                // if (in_array($key, $columnsWithMultiselect)) {
                                //     $table .= "<th data-column-name=\"custom_{$key}\">Custom</th>";
                                // }
                            }
                            $table .= "<th>Actions</th>";
                            $table .= "</tr>";

                            // Filter dropdowns
                            $table .= "<tr class='dd-row'>";
                            foreach ($final_column_order as $key) {
                                $table .= "<td style='border:1px solid;'>";
                                $table .= "<select multiple name='" . $key . "[]' data-id='$key' class='multiselect' id='$key'>";

                                foreach (array_keys($uniqueValues[$key]) as $option) {
                                    $safeOption = htmlspecialchars($option ?? '');
                                    $selected = (isset($_POST[$key]) && in_array($option, $_POST[$key])) ? "selected" : "";
                                    $table .= "<option value=\"$safeOption\" $selected>$safeOption</option>";
                                }
                                $table .= "</select>";
                                $table .= "</td>";
                                // if (in_array($key, $columnsWithMultiselect)) {
                                //     // Dummy cell to match layout
                                //     $table .= "<td></td>";
                                // }
                            }

                            $table .= "</tr></thead><tbody>";

                            // Data rows
                            foreach ($all_rows as $row) {
                                $table .= "<tr>";
                                foreach ($final_column_order as $colName) {
                                    $articleNum = $row['article_number'];
                                    $rowId = $row['id'];
                                    $value = $row[$colName] ?? '';
                                    $safeValue = htmlspecialchars($value ?? '');

                                    if (filter_var($value, FILTER_VALIDATE_URL)) {
                                        $table .= '<td><a href="' . $safeValue . '" target="_blank">' . $safeValue . '</a></td>';
                                    } elseif (preg_match('#^https?://#i', $value)) {
                                        $table .= '<td><a href="' . $safeValue . '" target="_blank">' . $safeValue . '</a></td>';
                                    } elseif (preg_match('/^([a-z0-9\-]+\.)+[a-z]{2,}(\/[^\s]*)?$/i', $value)) {
                                        // Looks like a domain without http(s), add http://
                                        $prefixed = 'http://' . $safeValue;
                                        $table .= '<td><a href="' . $prefixed . '" target="_blank">' . $safeValue . '</a></td>';
                                    } elseif (isEditableCacheStatusColumn($colName) && !shouldSkipColumn($colName)) {       // to make td as select
                                        $table .= "<td class='editable-cell' data-column='{$colName}' data-row-id='{$articleNum}'>";
                                        $table .= "<div class='cell-display'>" . $safeValue . "</div>";

                                        $table .= "<select class='cell-editor' style='display:none'>";
                                        foreach (['Yes', 'No'] as $option) {
                                            $selected = ($option === $value) ? "selected" : "";
                                            $table .= "<option value=\"$option\" $selected>$option</option>";
                                        }
                                        $table .= "</select>";
                                        $table .= "</td>";
                                    } elseif (columnsWithSelectYesNo($colName)) {       // to make td as select
                                        $table .= "<td class='editable-cell' data-column='{$colName}' data-row-id='{$articleNum}'>";
                                        $table .= "<div class='cell-display'>" . $safeValue . "</div>";

                                        $table .= "<select class='cell-editor' style='display:none'>";
                                        foreach (['Yes', 'No'] as $option) {
                                            $selected = ($option === $value) ? "selected" : "";
                                            $table .= "<option value=\"$option\" $selected>$option</option>";
                                        }
                                        $table .= "</select>";
                                        $table .= "</td>";
                                    } elseif (isCommentColumn($colName)) {
                                        $table .= "<td class='editable-cell comment-cell' data-column='{$colName}' data-row-id='{$articleNum}'>";
                                        $table .= "<div class='cell-display'>" . $safeValue . "</div>";
                                        $table .= "<input type='text' class='cell-editor comment-editor' value='" . $safeValue . "' style='display:none; width: 100%;'>";

                                        $table .= "</td>";
                                    } elseif (isSelectValue($colName)) { // to make td as select for columns not in cache_status
                                        $table .= "<td class='editable-cell' data-column='{$colName}' data-row-id='{$articleNum}'>";
                                        $table .= "<div class='cell-display'>" . $safeValue . "</div>";

                                        $table .= "<select class='cell-editor' style='display:none'>";
                                        foreach (['Yes', 'No'] as $option) {
                                            $selected = ($option === $value) ? "selected" : "";
                                            $table .= "<option value=\"$option\" $selected>$option</option>";
                                        }
                                        $table .= "</select>";
                                        $table .= "</td>";
                                    } elseif (isMultiselectPluginColumn($colName)) {

                                        // $table .= "</select></td>";
                                        $table .= "<td class='editable-cell multiselect-cell' data-column='{$colName}' data-row-id='{$articleNum}'>";

                                        // The hidden native HTML multi-select (for data synchronization)
                                        $table .= "<select multiple class='cell-editor multiselect-editor' style='display:none' id='{$colName}-{$articleNum}-select'>";
                                        if (isset($pluginOptionsByType[$colName])) {
                                            foreach ($pluginOptionsByType[$colName] as $option) {
                                                $safeOption = htmlspecialchars($option);
                                                // Check if this option is currently selected for the row
                                                $selected = in_array($option, explode(',', $value)) ? "selected" : "";
                                                $table .= "<option value=\"$safeOption\" $selected>$safeOption</option>";
                                            }
                                        }
                                        $table .= "</select>";

                                        // The custom multi-select UI
                                        $table .= "<div class='custom-multiselect-container' data-target-select='{$colName}-{$articleNum}-select'>";
                                        $table .= "<div class='selected-items-display'></div>"; // Where selected tags will appear
                                        $table .= "<div class='custom-multiselect-options'></div>"; // Where all options will appear
                                        $table .= "</div>"; // End custom-multiselect-container

                                        $table .= "</td>";
                                    } else {
                                        $table .= '<td>' . $safeValue . '</td>';
                                    }

                                    //  {
                                    //     $table .= '<td>' . $safeValue . '</td>';
                                    // }
                                    // Custom multiselect after specific columns
                                    // if (in_array($colName, $columnsWithMultiselect)) {
                                    //     $table .= "<td><select multiple class='custom-multiselect' name='custom_{$colName}[{$rowId}][]'>";
                                    //     // $table .= "<option value='Option 1'>Option 1</option>";
                                    //     // $table .= "<option value='Option 2'>Option 2</option>";
                                    //     // $table .= "<option value='Option 3'>Option 3</option>";
                                    //     $table .= "</select></td>";
                                    // }
                                }
                                //for edit row store id into hidden input
                                $table .= "<td style='border:1px solid;'>
                                 <input type='hidden' name='row_ids[]' value='{$rowId}'>
                                 <a href='edit_projects.php?id={$rowId}'><i class='far fa-edit'></i></a>
                                 </td>";
                                $table .= "</tr>";
                            }

                            $table .= "</tbody></table>";

                            // $table .= "<br><input type='submit' value='Filter'>";
                            $table .= "</form>";
                            $table .= "</div>";
                            // $table .= "<span style='color:rgb(0, 126, 219); position:absolute; top:-6px;'><strong>Showing</strong> {$count} <strong>of</strong> {$totalCount}</span>";
                        } else {
                            $table .= "<p> Data Not Available .</p>";
                        }
                        echo $table;
                    } else {
                        echo "Query failed: " . mysqli_error($conn);
                    }
                    ?>
                    <div class="list-contain">
                        <!-- <a href="#" class="side-list-toggle"> Column List</a> -->

                        <div class="side-list" style="padding: 6px; border: 1px solid #ccc; background: #fff; width: 200px;">
                            <!-- Search Box -->
                            <input type="text" id="columnSearch" placeholder="Search..." style="width: 100%; margin-bottom: 10px;">

                            <!-- Column List -->
                            <div id="columnList">
                                <?php $count = 1; ?>
                                <?php foreach ($all_fields as $field): ?>
                                    <div style="width:250px; padding: 4px 0; border-bottom: 1px solid #eee; cursor: pointer;"
                                        class="column-link"
                                        data-column="<?php echo htmlspecialchars($field->name); ?>">
                                        <span style="color: #666; margin-right: 5px;"><?php echo $count++; ?>.</span>
                                        <?php echo htmlspecialchars($field->name); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollWrapper = document.querySelector('.table-scroll-wrapper');
        const tbody = scrollWrapper.querySelector('tbody');
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
                const isInsideSideList = e.target.closest('.side-list');
                //&& !e.target.parentElement.parentElement.classList.contains('ulList')
                if (scrollWrapper.scrollHeight > scrollWrapper.clientHeight && !(e.target.closest('.ulList')) && !isInsideSideList) { // prevent from event trigger on side list
                    e.preventDefault();
                    scrollWrapper.scrollTop += e.deltaY;
                }
            }, {
                passive: false
            });
        }
    });
    //---------
    document.addEventListener('DOMContentLoaded', function() {
    const columnLinks = document.querySelectorAll('.column-link');
    const scrollWrapper = document.querySelector('.table-scroll-wrapper');
    const stickyColumnWidth = 130; // You can adjust this value

    columnLinks.forEach(link => {
        link.addEventListener('click', function() {
            const columnName = this.dataset.column;
            const targetHeader = document.querySelector(`th[data-column-name="${columnName}"]`);
            const extraSpacing = 10;

            if (targetHeader && scrollWrapper) {
                // Use scrollIntoView with offset
                targetHeader.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'start'
                });

                // Offset scroll to stop before sticky column
                setTimeout(() => {
                    scrollWrapper.scrollLeft = targetHeader.offsetLeft - stickyColumnWidth - extraSpacing;
                }, 370); // Delay to ensure scrollIntoView completes first
            }
        });
    });
});

    // Search functionality
    document.getElementById('columnSearch').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const items = document.querySelectorAll('#columnList .column-link');

        items.forEach(function(item) {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(filter) ? '' : 'none';
        });
    });
    //---------------------------------
    document.body.addEventListener('change', function(e) {
        if (e.target.classList.contains('multiselect-editor')) { // <--- This targets the hidden native select
            const nativeSelect = e.target;
            const cell = nativeSelect.closest('.editable-cell');
            const column = cell.dataset.column;
            const rowId = cell.dataset.rowId;

            // Get all selected values from the native select
            const selectedValues = Array.from(nativeSelect.selectedOptions).map(option => option.value);
            const newValue = selectedValues.join(','); // Join with comma for saving

            // Update the display div
            const display = cell.querySelector('.cell-display');
            display.textContent = newValue; // Update the visible text to the comma-separated list

            // --- HERE  INSERT EXISTING AJAX CALL ---
            console.log(`Saving multiselect data for ${column} (Article No: ${rowId}): ${newValue}`);
        }
    });
</script>
<script src="/assets/dist/js/view_sheet.js"></script>
<?php include('includes/footer.php'); ?>