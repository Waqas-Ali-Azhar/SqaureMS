<?php
include_once 'includes/header.php';
include 'includes/topbar.php';
include 'includes/sidebar.php';

$client_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch client data
$client = [];
if ($client_id > 0) {
    $query = "SELECT * FROM clients WHERE id = $client_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $client = $result->fetch_assoc();
    } else {
        echo "<script>alert('Client not found.'); window.location.href='clients.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid client ID.'); window.location.href='clients.php';</script>";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $address = $_POST['address'];
    $update = "UPDATE clients SET 
                name='$name',
                phone_number='$phone_number',
                email='$email',
                city='$city',
                country='$country',
                status='$status',
                address = '$address'
               WHERE id = $client_id";
    if (mysqli_query($conn, $update)) {
        $updatelead = $conn->query("UPDATE leads SET name = '$name', phone = $phone_number, email = '$email', country = '$country', city = '$city', address = '$address'  WHERE client_id = $client_id");
        echo "<script>alert('Client updated successfully!'); window.location.href='clients.php';</script>";
    } else {
        echo "Error updating client: " . mysqli_error($conn);
    }
}
// Handle deletion via ?delete=1
if (isset($_GET['delete']) && $_GET['delete'] == 1 && $client_id > 0) {
    $deleteQuery = "DELETE FROM clients WHERE id = $client_id";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('Client deleted successfully.'); window.location.href='clients.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting client: " . mysqli_error($conn) . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Client</title>
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

        .edit-form input,
        .edit-form select {
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

    <form action="edit_client.php?id=<?= $client_id ?>" method="POST" class="edit-form">
        <h2>Edit Client (ID: <?= $client_id ?>)</h2>

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($client['name']) ?>">
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" required value="<?= htmlspecialchars($client['phone_number']) ?>">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($client['email']) ?>">
        </div>

        <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" value="<?= htmlspecialchars($client['city']) ?>">
        </div>

        <div class="form-group">
            <label for="country">Country</label>
            <input type="text" id="country" name="country" value="<?= htmlspecialchars($client['country']) ?>">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?= $client['address'] ?>">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <input type="text" id="status" name="status" value="<?= $client['status'] ?>">
        </div>

        <button type="submit">Update Client</button>
    </form>

</body>

</html>

<?php include('includes/footer.php'); ?>