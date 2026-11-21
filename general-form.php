<?php
require_once __DIR__ . '/config/database.php';
include 'includes/lib/main_lib.php';
verify_user();

// Helper: Generate slug
function generateSlug($string)
{
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

$isEdit = false;
$data = ['name' => '', 'slug' => '', 'url' => '', 'thumbnail' => ''];
$selectedParents = [];

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];

    $result = mysqli_query($conn, "SELECT * FROM general_data WHERE id = $id");
    $data = mysqli_fetch_assoc($result) ?: $data;

    $parentQuery = mysqli_query($conn, "SELECT master_id FROM combined_data WHERE general_id = $id");
    while ($row = mysqli_fetch_assoc($parentQuery)) {
        $selectedParents[] = $row['master_id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = generateSlug($name);
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    $parents = $_POST['parent'] ?? [];

    $thumbnail = $data['thumbnail'] ?? '';

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $folder = 'uploads/';
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $fileName = time() . '-' . basename($_FILES['thumbnail']['name']);
        $filePath = $folder . $fileName;

        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $filePath)) {
            $thumbnail = $fileName;
        }
    }

    if ($isEdit) {
        mysqli_query($conn, "UPDATE general_data SET name='$name', slug='$slug', url='$url', thumbnail='$thumbnail' WHERE id=$id");

        foreach ($parents as $parent_id) {
            $parent_id = (int)$parent_id;
            mysqli_query($conn, "INSERT INTO combined_data (general_id, master_id) VALUES ($id, $parent_id)");
        }

        header("Location: general.php?updated=1");
        exit();
    } else {
        mysqli_query($conn, "INSERT INTO general_data (name, slug, url, thumbnail) VALUES ('$name', '$slug', '$url', '$thumbnail')");
        $general_id = mysqli_insert_id($conn);

        foreach ($parents as $parent_id) {
            $parent_id = (int)$parent_id;

            mysqli_query($conn, "INSERT INTO combined_data (general_id, master_id) VALUES ($general_id, $parent_id)");
        }

        header("Location: general.php?added=1");
        exit();
    }
}

// Fetch parent dropdown options
$parentResult = mysqli_query($conn, "SELECT id, name FROM master_data");
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
                        <label for="parent">Select Parent</label>
                        <select name="parent[]" multiple id="parent">
                            <?php while ($row = mysqli_fetch_assoc($parentResult)): ?>
                                <option value="<?= $row['id'] ?>" <?= in_array($row['id'], $selectedParents) ? 'selected' : '' ?>>
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

<?php require('includes/footer.php'); ?>