<?php
require_once __DIR__ . '/config/database.php';
include_once 'includes/lib/main_lib.php';
verify_user();

// edit ka code ha ya 


// Helper: Generate slug from name
function generateSlug($string)
{
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

$isEdit = false;
$data = ['Name' => '', 'Slug' => '', 'URL' => '', 'Thumbnail' => ''];


if (isset($_GET['id'])) {
    $isEdit = true;
    $id = (int)$_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM master_data WHERE id = $id");
    $data = mysqli_fetch_assoc($result);
  
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = generateSlug($name);
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    $thumbnail = $data['Thumbnail'] ?? '';

    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $folder = 'uploads/';
        if (!is_dir($folder)) mkdir($folder, 0777, true);
        $fileName = time() . '-' . basename($_FILES['thumbnail']['name']);
        $filePath = $folder . $fileName;

        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $filePath)) {
            $thumbnail = $fileName;
        }
    }

    if ($isEdit) {
        $sql = "UPDATE master_data SET name='$name', slug='$slug', url='$url', thumbnail='$thumbnail' WHERE id=$id";
    } else {
        $sql = "INSERT INTO master_data (name, slug, url, thumbnail) VALUES ('$name', '$slug', '$url', '$thumbnail')";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: master.php?success=1");
        exit();
    } else {
        echo "<script>alert('Database Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>





<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div><!-- /.col -->
                <div class="col-sm-6">

                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->



    <div class="container-fluid  main-form-contain text-center mt-5 mb-5 ">

        <div class="row">
            <div class="col-lg-6 multi-step-main">



                <form method="POST" enctype="multipart/form-data">

                    <div class="form-step" id="step-1">

                        <div class="mb-3 multi-step-input">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($data['Name']) ?>" required>
                        </div>

                        <div class="mb-3 multi-step-input">
                            <label for="slug">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?= htmlspecialchars($data['Slug']) ?>" required readonly>

                        </div>
                        <div class="mb-3 multi-step-input">
                            <label for="url">URL</label>
                            <input type="url" class="form-control" id="url" name="url" value="<?= htmlspecialchars($data['URL']) ?>" required>
                        </div>

                        <div class="image-upload-wrapper">
                            <div class="thumb-nailimg-text">
                                <label>Thumbnail Preview</label>
                                <div>
                                    <img id="thumbnailPreview" src="<?= !empty($data['Thumbnail']) ? 'uploads/' . $data['Thumbnail'] : './assets/dist/img/gallery.jpg' ?>" alt="Image Preview" width="100" />
                                </div>
                            </div>


                            <div class="button-group">
                                <label for="thumbnail">Choose File</label>
                                <button type="button" onclick="removeImage()">Remove File</button>
                            </div>
                            <input type="file" id="thumbnail" name="thumbnail" class="custom-file-input" accept="image/*" onchange="previewThumbnail(event)">
                        </div>

                    </div>

                    <div class="button-group button-group-btn">
                        <button type="submit" name="submit"><?= $isEdit ? 'Update' : 'Submit' ?></button>
                    </div>
                </form>

            </div>
        </div>




    </div>
</div>

<?php
include('includes/footer.php');
?>