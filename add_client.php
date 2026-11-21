<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

//---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name          = mysqli_real_escape_string($conn, $_POST['name']);
    $phone_number  = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $email         = mysqli_real_escape_string($conn, $_POST['email']);
    $created_date  = mysqli_real_escape_string($conn, $_POST['created_date']);
    $status        = mysqli_real_escape_string($conn, $_POST['status']);
    $city          = mysqli_real_escape_string($conn, $_POST['city']);
    $country       = mysqli_real_escape_string($conn, $_POST['country']);

    // Insert query
    $insertQuery = "INSERT INTO clients (
                        name, phone_number, email, created_date, status, city, country
                    ) VALUES (
                        '$name', '$phone_number', '$email', '$created_date', '$status', '$city', '$country'
                    )";

    if (mysqli_query($conn, $insertQuery)) {
        echo "<script>alert('Client added successfully!'); window.location.href='add_client.php';</script>";
    } else {
        echo "Error: " . $insertQuery . "<br>" . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
//---
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Client Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .project-form {
            background: #fff;
            padding: 25px 30px;
            max-width: 700px;
            margin: 0 auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .project-form h2 {
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-col {
            flex: 1;
            min-width: 240px;
        }

        .project-form label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .project-form input,
        .project-form select,
        .project-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .project-form button {
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #007bff;
            border: none;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .project-form button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <form action="/add_client.php" method="POST" class="project-form">
        <h2>Add Client</h2>

        <div class="form-row">
            <div class="form id='data-form' name='data-form' method='POST' class='form-filter'">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group form-col">
                <label for="phone_number">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-col">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-col">
                <label for="created_date">Created Date</label>
                <input type="date" id="created_date" name="created_date" required>
            </div>

            <div class="form-group form-col">
                <label for="status">Status</label>
                <input type="text" id="status" name="status">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-col">
                <label for="city">City</label>
                <input type="text" id="city" name="city">
            </div>

            <div class="form-group form-col">
                <label for="country">Country</label>
                <input type="text" id="country" name="country">
            </div>
        </div>

        <button type="submit">Submit</button>
    </form>

</body>

</html>

<?php include('includes/footer.php'); ?>