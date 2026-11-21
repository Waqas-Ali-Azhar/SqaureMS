<?php
include_once 'includes/header.php';
include 'includes/topbar.php';
include 'select2links.php';
include 'includes/sidebar.php';
require_once __DIR__ . '/config/database.php';
// previous page
$row = [];
$id = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $fields = $_POST;
    unset($fields['id']);

    $updateParts = [];
    foreach ($fields as $key => $value) {
        if ($key == 'article_number' || $key == 'allowed_url' || $key == 'referer') continue;
        $cleanKey = mysqli_real_escape_string($conn, $key);
        if ($value === '') {
            $updateParts[] = "`$cleanKey` = NULL";
        } else {
            $cleanValue = mysqli_real_escape_string($conn, $value);
            $updateParts[] = "`$cleanKey` = '$cleanValue'";
        }
    }
    //------------------------------------
    $projectId = intval($_POST['id']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($fileExtension, $allowedExtensions) && $fileSize <= 5 * 1024 * 1024) {
            $newFileName = uniqid('thumbnail_', true) . '.' . $fileExtension;
            $uploadDir = 'uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $escapedPath = mysqli_real_escape_string($conn, $destPath);

                // Check if a thumbnail already exists for this project
                $checkQuery = "SELECT thumbnail FROM project_logos WHERE project_id = $projectId";
                $checkResult = mysqli_query($conn, $checkQuery);

                if ($checkResult && mysqli_num_rows($checkResult) > 0) {
                    $row = mysqli_fetch_assoc($checkResult);
                    $oldLogoPath = $row['thumbnail'];

                    // Delete old thumbnail file if it exists
                    if (!empty($oldLogoPath) && file_exists($oldLogoPath)) {
                        unlink($oldLogoPath);
                    }

                    // Update with new thumbnail
                    $updateQuery = "UPDATE project_logos SET thumbnail = '$escapedPath' WHERE project_id = $projectId";
                    if (!mysqli_query($conn, $updateQuery)) {
                        echo "<div class='alert alert-warning'>Failed to update logo in DB: " . mysqli_error($conn) . "</div>";
                    }
                } else {
                    // Insert new thumbnail
                    $insertQuery = "INSERT INTO project_logos (project_id, thumbnail) VALUES ($projectId, '$escapedPath')";
                    if (!mysqli_query($conn, $insertQuery)) {
                        echo "<div class='alert alert-warning'>Image uploaded but not stored in DB: " . mysqli_error($conn) . "</div>";
                    }
                }
            } else {
                echo "<div class='alert alert-danger'>Failed to move uploaded file.</div>";
            }
        } else {
            echo "<script>alert('Invalid image. Only JPG, PNG, GIF, or WEBP files under 5MB are allowed.')</script>";
        }
    }

    //------------------------------------


    $updateQuery = "UPDATE new_projects SET " . implode(', ', $updateParts) . " WHERE id = $id";
    $updateQuery2 = "UPDATE master_sheet SET " . implode(', ', $updateParts) . " WHERE id = $id";
    $ok1 = mysqli_query($conn, $updateQuery);
    $ok2 = mysqli_query($conn, $updateQuery2);
    //----------------------------------------
    $articleNumber = mysqli_real_escape_string($conn, $_POST['article_number'] ?? '');
    $sslValue = mysqli_real_escape_string($conn, $_POST['ssl_installed'] ?? '');
    $cachingValue = mysqli_real_escape_string($conn, $_POST['cache_status'] ?? '');
    $aggregationStatusCss = mysqli_real_escape_string($conn, $_POST['aggregation_status_css'] ?? '');
    $aggregationStatusJs = mysqli_real_escape_string($conn, $_POST['aggregation_status_js'] ?? '');

    if ($articleNumber !== '') {
        // --- Update ssl_status table ---
        $checkSSL = mysqli_query($conn, "SELECT 1 FROM ssl_status WHERE article_number = '$articleNumber'");
        if ($checkSSL && mysqli_num_rows($checkSSL) > 0) {
            mysqli_query($conn, "UPDATE ssl_status SET ssl_status = '$sslValue' WHERE article_number = '$articleNumber'");
        }
        // --- Update cache_status table ---
        $checkCache = mysqli_query($conn, "SELECT 1 FROM cache_status WHERE article_number = '$articleNumber'");
        if ($checkCache && mysqli_num_rows($checkCache) > 0) {
            mysqli_query($conn, "UPDATE cache_status SET cache_status = '$cachingValue', aggregation_status_css = '$aggregationStatusCss', aggregation_status_js = '$aggregationStatusJs' WHERE article_number = '$articleNumber'");
        }
        // --- Update master_sheet table ---
        $checkMaster = mysqli_query($conn, "SELECT 1 FROM master_sheet WHERE article_number = '$articleNumber'");
        if ($checkMaster && mysqli_num_rows($checkMaster) > 0) {
            mysqli_query($conn, "UPDATE master_sheet SET ssl_installed = '$sslValue', cache_status = '$cachingValue', aggregation_status_css = '$aggregationStatusCss', aggregation_status_js = '$aggregationStatusJs' WHERE article_number = '$articleNumber'");
        }
    }

    //-------------------------------------------
    // After updating new_projects
    if (isset($_POST['allowed_url']) && is_array($_POST['allowed_url'])) {
        $articleNumber = $_POST['article_number'];
        $allowedUrls = $_POST['allowed_url'];
        $allowedUrlVal = implode(",", $allowedUrls);

        $selectQuery = "SELECT * FROM allowed_urls  WHERE article_number = $articleNumber";
        $result = mysqli_query($conn, $selectQuery);
        if (mysqli_num_rows($result) > 0) {
            $updateTable = "UPDATE allowed_urls SET allowed_url = '$allowedUrlVal' WHERE article_number = $articleNumber";

            $resultQuery = mysqli_query($conn, $updateTable);
        } else {
            $insertQuery = "INSERT INTO allowed_urls (article_number, allowed_url) VALUES ('$articleNumber', '$allowedUrlVal')";
            mysqli_query($conn, $insertQuery);
        }
    }


    if ($ok1 && $ok2) {
        echo "<script>
            alert('Successfully updated!');
            window.location.href = '" . $_REQUEST['referer'] . "';
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Update failed:<br>"
            . mysqli_error($conn) . "</div>";
    }
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM new_projects WHERE id = $id");

    if ($query && mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
    } else {
        echo "<p>No record found with ID: $id</p>";
        exit;
    }
} else {
    echo "<p>Invalid request: ID missing</p>";
    // exit;
}
?>
<div class="content-wrapper" style="min-height: 589px;">
    <div class="container-fluid  main-form-contain text-left mt-5 mb-5 ">
        <h2>Edit Record ID: <?= $id ?></h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="referer" value="<?= $_SERVER['HTTP_REFERER'] ?>">

            <?php
            // Define dropdown fields and their options
            $dropdownColumns = [
                'ssl_installed' => ['Yes', 'No'],
                'public_view' => ['Yes', 'No'],
                'returns' => ['Yes', 'No'],
                'plugin_installed' => ['Yes', 'No'],
                'cache_status' => ['Yes', 'No'],
                'multi_demos' => ['Yes', 'No'],
                'multi_category' => ['Yes', 'No'],
                'builder_support' => ['No', 'Elementor', 'Visual Composer', 'WP Bakery', 'Site Origin', 'Gutenberg'],
                'custom_coded' => ['Yes', 'No'],
                'variation_products' => ['Yes', 'No'],
                'service_forms' => ['Yes', 'No'],
                'allowed_url' => ['options' => ['1', '2', '3', '4', '5'], 'multiple' => true],
                'aggregation_status_css' => ['Yes', 'No'],
                'aggregation_status_js' => ['Yes', 'No'],
            ];

            $counter = 0;
            echo '<div class="row">';
            foreach ($row as $column => $value):
                if ($column == 'id') continue;

                $label = ucwords(str_replace('_', ' ', $column));
                $safeValue = $value;  // Prevent XSS
                $inputType = 'text';
                $attributes = '';

                echo '<div class="col-md-6">';
                echo '<div class="form-group" style="width:80%;">';
                echo "<label for=\"$column\">$label</label>";

                // If column is in dropdownColumns, render <select>
                if (array_key_exists($column, $dropdownColumns)) {
                    // echo "<select name=\"$column\" id=\"$column\" class=\"form-control\">";
                    // foreach ($dropdownColumns[$column] as $option) {
                    //     $selected = ($safeValue == $option) ? 'selected' : '';
                    //     echo "<option value=\"$option\" $selected>$option</option>";
                    // }
                    // echo "</select>";
                    $isMultiple = false;
                    $options = $dropdownColumns[$column];

                    if (is_array($dropdownColumns[$column]) && isset($dropdownColumns[$column]['options'])) {
                        $isMultiple = $dropdownColumns[$column]['multiple'] ?? false;
                        $options = $dropdownColumns[$column]['options'];
                    }

                    // Handle multiple values
                    $selectedValues = $isMultiple ? explode(',', $safeValue) : [$safeValue];
                    $multipleAttr = $isMultiple ? 'multiple' : '';
                    $nameAttr = $isMultiple ? "{$column}[]" : $column;

                    echo "<select name=\"$nameAttr\" id=\"$column\" class=\"form-control select2\" $multipleAttr>";
                    foreach ($options as $option) {
                        $selected = in_array($option, $selectedValues) ? 'selected' : '';
                        echo "<option value=\"$option\" $selected>$option</option>";
                    }
                    echo "</select>";
                } elseif ($column == 'article_number') {
                    echo "<input type=\"text\" name=\"$column\" id=\"$column\" value=\"$safeValue\" class=\"form-control\" disabled>";
                    echo "<input type=\"hidden\" name=\"article_number\" value=\"$safeValue\">";
                } else {
                    echo "<input type=\"$inputType\" name=\"$column\" id=\"$column\" value=\"$safeValue\" class=\"form-control\">";
                }

                echo '</div>';
                echo '</div>';

                $counter++;
                if ($counter % 2 == 0) {
                    echo '</div><div class="row">'; // Close current row, start new one after every 2 columns
                }
            endforeach;
            echo '</div>'; // Close the last row
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="width: 80%;">
                        <label for="image">Upload Image</label>
                        <div class="input-group">
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('image').value = ''">Clear</button>
                        </div>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group multi-step-form" style=" width:20%;">
                        <div class="input-group">
                            <label for="submit"> Update form</label>
                            <input type="submit" value="Update" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select Allowed URLs",
        allowClear: true,
        width: '100%'
    });
});
</script>

<?php include('includes/footer.php'); ?>