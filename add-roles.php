<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
include('./');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newRole = trim($_POST['role']);

    if (!empty($newRole)) {
        $insertSql = "INSERT INTO roles (role) VALUES ('$newRole')";
        if ($conn->query($insertSql)) {
            echo "<script>alert('Role added successfully.'); window.location.href='manage_roles.php';</script>";
            exit;
        } else {
            echo "<script>alert('Failed to add role.');</script>";
        }
    } else {
        echo "<script>alert('Role cannot be empty.');</script>";
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
</style>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="form-container">
        <h3>Add New Role</h3>
        <form method="POST">
          <div class="mb-3">
            <label for="role" class="form-label">Role Name</label>
            <input type="text" name="role" id="role" class="form-control" required placeholder="Enter role name">
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-success btn-save">Add Role</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
