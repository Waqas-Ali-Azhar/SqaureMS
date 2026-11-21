<?php
include_once 'includes/header.php';
include 'includes/topbar.php';
include 'includes/sidebar.php';

$plugin_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch plugin data
$plugin = [];
if ($plugin_id > 0) {
    $query = "SELECT * FROM plugins WHERE id = $plugin_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $plugin = $result->fetch_assoc();
    } else {
        echo "<script>alert('Plugin not found.'); window.location.href='plugins.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid plugin ID.'); window.location.href='plugins.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plugin_name = mysqli_real_escape_string($conn, $_POST['plugin_name']);
    $plugin_type = mysqli_real_escape_string($conn, $_POST['plugin_type']);

    $update = "UPDATE plugins SET 
                plugin_name='$plugin_name',
                plugin_type='$plugin_type'
               WHERE id = $plugin_id";

    if (mysqli_query($conn, $update)) {
        echo "<script>alert('Plugin updated successfully!'); window.location.href='plugins.php';</script>";
    } else {
        echo "Error updating plugin: " . mysqli_error($conn);
    }
}

// Handle deletion via ?delete=1
if (isset($_GET['delete']) && $_GET['delete'] == 1 && $plugin_id > 0) {
    $deleteQuery = "DELETE FROM plugins WHERE id = $plugin_id";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('Plugin deleted successfully.'); window.location.href='plugins.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting plugin: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Plugin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        .edit-form {
            background: #fff;
            padding: 25px 30px;
            max-width: 700px;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .edit-form h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .edit-form label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .edit-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-form button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-form button:hover {
            background: #0056b3;
        }
    </style>
</head>

<body>

<div class="container mt-4">
    <form method="POST" class="edit-form">
        <h2>Edit Plugin (ID: <?= $plugin_id ?>)</h2>

        <div class="mb-3">
            <label for="plugin-name" class="form-label">Plugin Name</label>
            <input type="text" name="plugin_name" id="plugin-name" class="form-control" required 
                   value="<?= htmlspecialchars($plugin['plugin_name']) ?>" placeholder="Enter plugin name...">
        </div>

        <div class="mb-3">
            <label for="plugin-type" class="form-label">Select Type</label>
            <select class="form-control sel-type" name="plugin_type" required>
                <option value="" disabled <?= $plugin['plugin_type'] == '' ? 'selected' : '' ?>>Select</option>
                <option value="cache" <?= $plugin['plugin_type'] == 'cache' ? 'selected' : '' ?>>cache</option>
                <option value="optimization" <?= $plugin['plugin_type'] == 'optimization' ? 'selected' : '' ?>>optimization</option>
                <option value="image optimization" <?= $plugin['plugin_type'] == 'image optimization' ? 'selected' : '' ?>>image optimization</option>
                <option value="cdn" <?= $plugin['plugin_type'] == 'cdn' ? 'selected' : '' ?>>CDN</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Plugin</button>
    </form>
</div>

</body>

</html>

<?php include('includes/footer.php'); ?>
