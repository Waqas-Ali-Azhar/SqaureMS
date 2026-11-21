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
    <div class="container-fluid mt-5 mb-5 pl-4">
        <div class="row">
            <div class="col-lg-6 multi-step-main p-4 shadow bg-white rounded">
                <h3 class="text-center mb-4">Import Excel File Data</h3>
                <form action="import-data-process.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Choose Excel File</label>
                        <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xls,.xlsx" required>
                    </div>
                    <div class="text-center import-btn mt-4">
                        <button type="submit" name="import_data">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php
include('includes/footer.php');
?>