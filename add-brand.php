<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

//insert query form
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST !== '') {
    $brand = $_POST['brand'];
    $shortDescprition = $_POST['shortDescprition'];
    $longDescprition = $_POST['longDescprition'];
    $createdAt = $_POST['createdAt'];
    $updatedAt = $_POST['updatedAt'];
    $status = $_POST['status'];
    $specifics = $_POST['specifics'];
    $services = $_POST['services'];
    $budget = $_POST['budget'];
    $details = $_POST['details'];

    // Insert query
    $insertQuery = "INSERT INTO brands (brand, short_description, long_description, created_at, updated_at, status)
                    VALUES ('$brand', '$shortDescprition', '$longDescprition', '$createdAt','$updatedAt','$status' )";

    if (mysqli_query($conn, $insertQuery)) {
        // Redirect or display success message
        echo "<script>alert('Brand added successfully!'); window.location.href='add-brand.php';</script>";
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
    <title>Project Form</title>
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

        .project-form textarea {
            resize: vertical;
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

    <form action="/add-brand.php" method="POST" class="project-form">
        <h2>Add Brand </h2>

        <div class="form-row">
            <div class="form-group form-col">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" required>
            </div>

            <div class="form-group form-col">
                <label for="shortDescprition">Short Description</label>
                <input type="text" id="shortDescprition" name="shortDescprition" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-col">
                <label for="phone">Long Descprition</label>
                <input type="text" id="longDescprition" name="longDescprition" required>
            </div>

            <div class="form-group form-col">
                <label for="createdAt">Created At</label>
                <input type="date" id="createdAt" name="createdAt" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-col">
                <label for="updatedAt">Updated At</label>
                <input type="date" id="updatedAt" name="updatedAt" required>
            </div>

            <div class="form-group form-col">
                <label for="status">Status</label>
                <input type="text" id="status" name="status" required></input>
            </div>
        </div>

        <button type="submit">Submit</button>
    </form>

</body>

</html>
<?php include('includes/footer.php'); ?>