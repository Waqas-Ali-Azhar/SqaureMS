<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $short_discrption = mysqli_real_escape_string($conn, $_POST['shortDescription']);
    $long_discription = mysqli_real_escape_string($conn, $_POST['longDescription']);
    $created_at = mysqli_real_escape_string($conn, $_POST['createdAt']);
    $updated_at = mysqli_real_escape_string($conn, $_POST['updatedAt']);
    $assignee_id = mysqli_real_escape_string($conn, $_POST['assigneeId']);
    $assignable_type = mysqli_real_escape_string($conn, $_POST['assignableType']);
    $assignable_id = mysqli_real_escape_string($conn, $_POST['assignableId']);
    $assigned_by = mysqli_real_escape_string($conn, $_POST['assignedBy']);
    $assigned_at = mysqli_real_escape_string($conn, $_POST['assignedAt']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $insertQuery = "INSERT INTO assignments (
        title, short_discrption, long_discription, created_at, updated_at,
        assignee_id, assignable_type, assignable_id, assigned_by, assigned_at,
        notes, status
    ) VALUES (
        '$title', '$short_discrption', '$long_discription', '$created_at', '$updated_at',
        '$assignee_id', '$assignable_type', '$assignable_id', '$assigned_by', '$assigned_at',
        '$notes', '$status'
    )";

    if (mysqli_query($conn, $insertQuery)) {
        echo "<script>alert('Assignment added successfully!'); window.location.href='add_contract.php';</script>";
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

<form action="/add-assignment.php" method="POST" class="project-form">
    <h2>Add Assignment</h2>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group form-col">
            <label for="shortDescription">Short Description</label>
            <input type="text" id="shortDescription" name="shortDescription" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="longDescription">Long Description</label>
            <textarea id="longDescription" name="longDescription" required></textarea>
        </div>

        <div class="form-group form-col">
            <label for="notes">Notes</label>
            <input type="text" id="notes" name="notes">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="assigneeId">Assignee ID</label>
            <input type="text" id="assigneeId" name="assigneeId">
        </div>

        <div class="form-group form-col">
            <label for="assignableType">Assignable Type</label>
            <input type="text" id="assignableType" name="assignableType">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="assignableId">Assignable ID</label>
            <input type="text" id="assignableId" name="assignableId">
        </div>

        <div class="form-group form-col">
            <label for="assignedBy">Assigned By</label>
            <input type="text" id="assignedBy" name="assignedBy">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group form-col">
            <label for="assignedAt">Assigned At</label>
            <input type="date" id="assignedAt" name="assignedAt">
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
            <input type="text" id="status" name="status" required>
        </div>
    </div>

    <button type="submit">Submit</button>
</form>


</body>

</html>
<?php include('includes/footer.php'); ?>