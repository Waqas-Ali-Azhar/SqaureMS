<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
include('./');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $PluginName = trim($_POST['plugin-name']);
  $PluginType = isset($_POST['plugin-type']) ? $_POST['plugin-type'] : [];

  if (!empty($PluginName) && !empty($PluginType)) {
    $PluginTypeString = implode(',', $PluginType);
    $insertSql = "INSERT INTO plugins (plugin_name, plugin_type) VALUES ('$PluginName','$PluginTypeString')";
    if ($conn->query($insertSql)) {
      echo "<script>alert('Plugin added successfully.'); window.location.href='add-plugin.php';</script>";
      exit;
    } else {
      echo "<script>alert('Failed to add Plugin.');</script>";
    }
  } else {
    echo "<script>alert('Fields cannot be empty.');</script>";
  }
}
?>

<style>
  body {
    background-color: #f9f9f9;
  }

  .form-container {
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  h3 {
    font-weight: 600;
    margin-bottom: 25px;
  }

  .form-label {
    font-weight: 500;
    color: #333;
  }

  .btn-save {
    padding: 10px 30px;
    font-weight: 500;
  }

  .btn-back {
    margin-left: 10px;
  }

  .sel-type {
    /* appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none; */
    background-image: url('data:image/svg+xml;utf8,<svg fill="%23666" height="12" viewBox="0 0 20 20" width="12" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5H7z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 12px;
    padding-right: 30px;
  }
</style>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="form-container">
        <h3>Add New Plugin</h3>
        <form method="POST" action="/add-plugin.php">
          <div class="mb-3">
            <label for="plugin-name" class="form-label">Plugin Name</label>
            <input type="text" name="plugin-name" id="plugin" class="form-control" required placeholder="Enter plugin name..">
          </div>
          <div class="mb-3">
            <label class="form-label">Plugin Type</label><br>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="plugin-type[]" value="cache" id="cache">
              <label class="form-check-label" for="cache">Cache</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="plugin-type[]" value="optimization" id="optimization">
              <label class="form-check-label" for="optimization">Optimization</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="plugin-type[]" value="image optimization" id="imageOptimization">
              <label class="form-check-label" for="imageOptimization">Image Optimization</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="plugin-type[]" value="cdn" id="cdn">
              <label class="form-check-label" for="cdn">CDN</label>
            </div>
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-success btn-save">Add Plugin</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>