<?php
require_once __DIR__ . '/config/database.php';
include_once 'includes/lib/main_lib.php';
verify_user();

// Helper function to generate slug
function generateSlug($string)
{
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

$data = ['name' => '', 'slug' => '', 'url' => '', 'thumbnail' => ''];
$selectedParents = [];
$selectedParents2 = [];
$masterparents = $_POST['parent'] ?? [];
$generalparents = $_POST['parent2'] ?? [];
$isEdit = false;

// Check if editing
if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];


    // Fetch existing specific_data
    $result = mysqli_query($conn, "SELECT * FROM specific_data WHERE id = $id");

    //--------
    $g_result = mysqli_query($conn, "SELECT * FROM general_data WHERE id = $id");
    if ($g_result && mysqli_num_rows($g_result) > 0) {
        $g_data = mysqli_fetch_assoc($g_result);
    }
    $parentQuery = mysqli_query($conn, "SELECT master_id FROM combined_data WHERE general_id = $id");
    while ($row = mysqli_fetch_assoc($parentQuery)) {
        $selectedParents[] = $row['master_id'];
    }
    //--------
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    }

    // Fetch existing parent IDs
    $parentQuery2 = mysqli_query($conn, "SELECT general_id FROM specific_general_map WHERE specific_id = $id");
    while ($row = mysqli_fetch_assoc($parentQuery2)) {
        $selectedParents2[] = $row['general_id'];
    }
}

// Form Submit (Insert or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = generateSlug($name);
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    $masterparents = $_POST['parent'] ?? [];
    $parents = $_POST['parent2'] ?? [];

    $thumbnail = $data['thumbnail'] ?? '';

    // Upload new thumbnail if provided
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $folder = 'uploads/';
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $fileName = time() . '-' . basename($_FILES['thumbnail']['name']);
        $filePath = $folder . $fileName;

        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $filePath)) {
            $thumbnail = $fileName;
        }
    }
    // for master and general method
    // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //     foreach ($masterparents as $master_id) {
    //         foreach ($generalparents as $general_id) {
    //             $mstr_id = (int)$master_id;
    //             $gen_id = (int)$general_id;
    //             mysqli_query($conn, "INSERT INTO mgs_combined (master_id, general_id) VALUES ($mstr_id, $gen_id)");
    //         }
    //     }
    // }

    if ($isEdit) {
        // Update specific_data
        $updateQuery = "UPDATE specific_data SET name='$name', slug='$slug', url='$url', thumbnail='$thumbnail' WHERE id=$id";
        if (mysqli_query($conn, $updateQuery)) {
            mysqli_query($conn, "DELETE FROM specific_general_map WHERE specific_id = $id");

            foreach ($parents as $general_id) {
                $general_id = (int)$general_id;
                mysqli_query($conn, "INSERT INTO specific_general_map (specific_id, general_id) VALUES ($id, $general_id)");
            }

            foreach ($masterparents as $master_id) {
                foreach ($parents as $general_id) {
                    $mstr_id = (int)$master_id;
                    $gen_id = (int)$general_id;

                    $combinedInsert = "INSERT INTO mgs_combined (master_id, general_id, specific_id) 
                               VALUES ($mstr_id, $gen_id, $specific_id)";
                    if (!mysqli_query($conn, $combinedInsert)) {
                        echo "Error inserting into mgs_combined: " . mysqli_error($conn);
                    }
                }
            }
            header("Location: specific.php?updated=1");
            exit();
        } else {
            echo "Error updating data: " . mysqli_error($conn);
        }
    } else {
        $insertQuery = "INSERT INTO specific_data (name, slug, url, thumbnail) VALUES ('$name', '$slug', '$url', '$thumbnail')";
        if (mysqli_query($conn, $insertQuery)) {
            $specific_id = $conn->insert_id;
            // $last_id = $conn->insert_id;
            foreach ($parents as $general_id) {
                $general_id = (int)$general_id;
                mysqli_query($conn, "INSERT INTO specific_general_map (specific_id, general_id) VALUES ($specific_id, $general_id)");
            }
            foreach ($masterparents as $master_id) {
                foreach ($parents as $general_id) {
                    $mstr_id = (int)$master_id;
                    $gen_id = (int)$general_id;

                    $queryinsert = "INSERT INTO mgs_combined (master_id, general_id, specific_id)
                  VALUES ($mstr_id, $gen_id, $specific_id)";
                    if (!mysqli_query($conn, $queryinsert)) {
                        echo "Error inserting into mgs_combined: " . mysqli_error($conn);
                    }
                }
            }
         
            header("Location: specific.php?added=1");
            exit();
        } else {
            echo "Error inserting data: " . mysqli_error($conn);
        }
    }
}

$parentResult = mysqli_query($conn, "SELECT id, name FROM master_data");
$parentResult2 = mysqli_query($conn, "SELECT id, name FROM general_data");
?>


<?php include('includes/header.php'); ?>
<?php include('includes/topbar.php'); ?>
<?php include('includes/sidebar.php'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2"></div>
        </div>
    </div>

    <div class="container-fluid main-form-contain text-center mt-5 mb-5">
        <div class="row">
            <div class="col-lg-6 multi-step-main">
                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3 multi-step-input">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($data['name']) ?>" required>
                    </div>

                    <div class="mb-3 multi-step-input">
                        <label for="slug">Slug</label>
                        <input type="text" class="form-control" name="slug" id="slug" value="<?= htmlspecialchars($data['slug']) ?>" readonly required>
                    </div>

                    <div class="mb-3 multi-step-input">
                        <label for="url">URL</label>
                        <input type="url" class="form-control" name="url" id="url" value="<?= htmlspecialchars($data['url']) ?>" required>
                    </div>


                    <div class="mb-3 multi-step-input multi-step-form">
                        <label for="parent">Select Master</label>
                        <select name="parent[]" multiple class="form-control select2" id="parent">
                            <?php while ($row = mysqli_fetch_assoc($parentResult)): ?>
                                <option value="<?= htmlspecialchars($row['id']) ?>" <?= in_array($row['id'], $selectedParents) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>


                    <div class="mb-3 multi-step-input multi-step-form">
                        <label for="parent2">Select General</label>
                        <select name="parent2[]" multiple class="form-control select2" id="parent2">
                            <?php while ($row = mysqli_fetch_assoc($parentResult2)): ?>
                                <option value="<?= htmlspecialchars($row['id']) ?>" <?= in_array($row['id'], $selectedParents2) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>


                    <div class="image-upload-wrapper">
                        <div class="thumb-nailimg-text">
                            <label>Thumbnail Preview</label>
                            <div>
                                <img id="thumbnailPreview" src="<?= !empty($data['thumbnail']) ? 'uploads/' . $data['thumbnail'] : './assets/dist/img/gallery.jpg' ?>" alt="Preview" width="100" />
                            </div>
                        </div>

                        <div class="button-group">
                            <label for="thumbnail" style="cursor: pointer;">Choose File</label>
                            <button type="button" onclick="removeImage()">Remove File</button>
                        </div>
                        <input type="file" id="thumbnail" name="thumbnail" class="custom-file-input" accept="image/*" onchange="previewThumbnail(event)">
                    </div>

                    <div class="button-group button-group-btn mt-3">
                        <button type="submit" name="submit"><?= $isEdit ? 'Update' : 'Submit' ?></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>