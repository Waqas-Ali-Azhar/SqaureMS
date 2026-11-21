<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
include './newproject_dbcon.php';

?>
<?php
// while ($row = $result->fetch_assoc()) {
// echo '<pre>';
// print_r($row);
// echo '</pre>';
// exit();
// }
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
            <div class="multi-step-main">

                <form method="POST" enctype="multipart/form-data" id='newProjectFrom' action="newproject_dbcon.php">

                    <div class="form-step" id="step-1">
                        <div class="row">
                            <div class="col-lg-6 mb-3 multi-step-input form-new-input">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="col-lg-6 mb-3 multi-step-input form-new-input">
                                <div class="dropdown">

                                    <label for="nameSelect">Catagory</label>
                                    <select class="form-control" id="nameSelect" name="nameSelect" required>

                                        <option value="">Select</option>
                                        <!-- <option value="Ali">Ali</option>
                                    <option value="Sara">Sara</option>
                                    <option value="Ahmed">Ahmed</option> -->
                                        <?php

                                        // while ($row = mysqli_fetch_assoc($result)) {
                                        //     $label = "{$row['master_name']} > {$row['general_name']} > {$row['specific_name']}";
                                        //     echo "<option value='{$row['id']}'>$label</option>";
                                        // }
                                         while ($row = mysqli_fetch_assoc($result)) {
                                            // $label = "{$row['master_name']} > {$row['general_name']} > {$row['specific_name']}";
                                            echo "<option value='{$row['master_name']}>{$row['general_name']}>{$row['specific_name']}'>{$row['master_name']}>{$row['general_name']}>{$row['specific_name']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <!-- <i class="fa-solid fa-chevron-down"></i> -->
                                </div>
                            </div>
                        </div>

                        <!-- <div class="mb-3 multi-step-input">
                            <label for="slug">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="Slug" required readonly>

                        </div> -->
                        <div class="row">

                            <div class="col-lg-6 mb-3 multi-step-input form-new-input">
                                <label for="url">URL</label>
                                <input type="url" class="form-control" name="url" id="url" required>
                            </div>
                            <div class="col-lg-6 mb-3 multi-step-input form-new-input">
                                <label for="url">URL Text</label>
                                <input type="url" class="form-control" name="url-text" id="url-text" required>
                            </div>
                        </div>

                        <!-- <div class="row">
                            <div class="col-lg-6 image-upload-wrapper">
                                <div class="thumb-nailimg-text">
                                    <label>Thumbnail Preview</label>
                                    <div class="preview-img-one">
                                        <img id="thumbnailPreview" src="./assets/dist/img/gallery.jpg" alt="Image Preview" width="100" />
                                    </div>



                                    <div class="button-group">
                                        <label for="thumbnail">Choose File</label>
                                        <button type="button" onclick="removeImage()">Remove File</button>
                                    </div>
                                    <input type="file" id="thumbnail" name="thumbnail" class="custom-file-input" accept="image/*" onchange="previewThumbnail(event)">
                                </div>
                            </div>
                            <div class="col-lg-6 image-upload-wrapper">
                                <div class="thumb-nailimg-text">
                                    <label>Thumbnail Preview</label>
                                    <div>
                                        <img id="thumbnailPreview" src="./assets/dist/img/gallery.jpg" alt="Image Preview" width="100" />
                                    </div>



                                    <div class="button-group">
                                        <label for="thumbnail">Choose File</label>
                                        <button type="button" onclick="removeImage()">Remove File</button>
                                    </div>
                                    <input type="file" id="thumbnail" name="thumbnail" class="custom-file-input" accept="image/*" onchange="previewThumbnail(event)">
                                </div>
                            </div>

                        </div> -->

                        <div class="row">
                            <div class="col-lg-6 image-upload-wrapper">
                                <div class="thumb-nailimg-text">
                                    <label>Image Preview</label>
                                    <div class="preview-img-one">
                                        <img id="thumbnailPreview1" src="./assets/dist/img/gallery.jpg" alt="Image Preview" width="100" />
                                    </div>

                                    <div class="button-group">
                                        <label for="thumbnail1">Choose File</label>
                                        <button type="button" onclick="removeImage1('thumbnail1', 'thumbnailPreview1')">Remove File</button>
                                    </div>
                                    <input type="file" id="thumbnail1" name="thumbnail1" class="custom-file-input" accept="image/*" onchange="previewThumbnail(event, 'thumbnailPreview1')">
                                </div>
                            </div>
                            <div class="col-lg-6 image-upload-wrapper">
                                <div class="thumb-nailimg-text">
                                    <label>Thumbnail Preview</label>
                                    <div>
                                        <img id="thumbnailPreview2" src="./assets/dist/img/gallery.jpg" alt="Image Preview" width="100" />
                                    </div>

                                    <div class="button-group">
                                        <label for="thumbnail2">Choose File</label>
                                        <button type="button" onclick="removeImage2('thumbnail2', 'thumbnailPreview2')">Remove File</button>
                                    </div>
                                    <input type="file" id="thumbnail2" name="thumbnail2" class="custom-file-input" accept="image/*" onchange="previewThumbnail(event, 'thumbnailPreview2')">
                                </div>
                            </div>
                        </div>



                        <div class="button-group button-group-btn">
                            <button type="submit" name="submit">Submit</button>
                        </div>
                </form>

            </div>
        </div>




    </div>
</div>

<?php
include('includes/footer.php');
?>