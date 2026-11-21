<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
include('./');

// Get role ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid Role ID'); window.location.href='roles.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Fetch role from DB
$sql = "SELECT * FROM roles WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<script>alert('Role not found'); window.location.href='roles.php';</script>";
    exit;
}

$role = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedRole = trim($_POST['role']);
    if (!empty($updatedRole)) {
        $updateSql = "UPDATE roles SET role = '$updatedRole' WHERE id = $id";
        if ($conn->query($updateSql)) {
            echo "<script>alert('Role updated successfully.'); window.location.href='manage_roles.php';</script>";
            exit;
        } else {
            echo "<script>alert('Failed to update role.');</script>";
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
        <h3>Edit Role</h3>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Role ID</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($role['id']) ?>" readonly>
          </div>

          <div class="mb-3">
            <label for="role" class="form-label">Role Name</label>
            <input type="text" name="role" id="role" class="form-control" required value="<?= htmlspecialchars($role['role']) ?>">
          </div>

          <div class="text-end">
            <button type="submit" class="btn btn-primary btn-save">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
