<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $short_discrption = mysqli_real_escape_string($conn, $_POST['short_discrption']);
    $long_discription = mysqli_real_escape_string($conn, $_POST['long_discription']);
    $created_at = mysqli_real_escape_string($conn, $_POST['created_at']);
    $updated_at = mysqli_real_escape_string($conn, $_POST['updated_at']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $portfolio_id = mysqli_real_escape_string($conn, $_POST['portfolio_id']);
    $assigned_to = mysqli_real_escape_string($conn, $_POST['assigned_to']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);

    $insertQuery = "
        INSERT INTO tasks (
            title, short_discrption, long_discription, created_at, updated_at,
            status, portfolio_id, assigned_to, due_date, priority
        ) VALUES (
            '$title', '$short_discrption', '$long_discription', '$created_at', '$updated_at',
            '$status', '$portfolio_id', '$assigned_to', '$due_date', '$priority'
        )
    ";

    if (mysqli_query($conn, $insertQuery)) {
        echo "<script>alert('Task added successfully!'); window.location.href='tasks.php';</script>";
    } else {
        echo "Error: " . $insertQuery . "<br>" . mysqli_error($conn);
    }
}
//---
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- <title>Add Contract</title> -->
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

<form action="/add-task.php" method="POST" class="project-form">
    <h2>Add Task</h2>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group form-col">
            <label for="short_discrption">Short Description</label>
            <input type="text" id="short_discrption" name="short_discrption" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="long_discription">Long Description</label>
            <input type="text" id="long_discription" name="long_discription" required></textarea>
        </div>

        <div class="form-group form-col">
            <label for="portfolio_id">Portfolio ID</label>
            <input type="number" id="portfolio_id" name="portfolio_id">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="assigned_to">Assigned To</label>
            <input type="number" id="assigned_to" name="assigned_to">
        </div>

        <div class="form-group form-col">
            <label for="due_date">Due Date</label>
            <input type="date" id="due_date" name="due_date">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="priority">Priority</label>
            <select id="priority" name="priority">
                <option value="">Select priority</option>
                <option value="Low">Low</option>
                <option value="Normal">Normal</option>
                <option value="High">High</option>
                <option value="Critical">Critical</option>
            </select>
        </div>

        <div class="form-group form-col">
            <label for="status">Status</label>
            <input type="text" id="status" name="status" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="created_at">Created At</label>
            <input type="date" id="created_at" name="created_at" required>
        </div>

        <div class="form-group form-col">
            <label for="updated_at">Updated At</label>
            <input type="date" id="updated_at" name="updated_at" required>
        </div>
    </div>

    <button type="submit">Submit</button>
</form>


</body>

</html>
<?php include('includes/footer.php'); ?>