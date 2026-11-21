<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

//insert query form
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST !== '') {
    $leadId = $_POST['lead_id'];
    $leadName = $_POST['lead_name'];
    $contactInfo = $_POST['contact_info'];
    $leadSource = $_POST['lead_source'];
    $leadStatus = $_POST['lead_status'];
    $estimatedCloseDate = $_POST['estimated_close_date'];


    // Insert query
    $insertQuery = "INSERT INTO estimation (lead_id, lead_name, contact_info, lead_source, lead_status, estimated_close_date)
                VALUES ('$leadId', '$leadName', '$contactInfo', '$leadSource', '$leadStatus', '$estimatedCloseDate')";

    if (mysqli_query($conn, $insertQuery)) {
        // Redirect or display success message
        echo "<script>alert('Estimation added successfully!'); window.location.href='add-estimation.php';</script>";
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

    <form action="/add-estimation.php" method="POST" class="project-form">
        <h2>Add Estimation </h2>

        <div class="form-row">
            <div class="form-group form-col">
                <label for="leadId">Lead ID</label>
                <input type="text" id="leadId" name="lead_id">
            </div>
            <div class="form-group form-col">
                <label for="leadName">Lead Name</label>
                <input type="text" id="leadName" name="lead_name">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group form-col">
                <label for="contactInfo">Contact Info</label>
                <input type="text" id="contactInfo" name="contact_info">
            </div>
            <div class="form-group form-col">
                <label for="leadSource">Lead Source</label>
                <input type="text" id="leadSource" name="lead_source">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group form-col">
                <label for="leadStatus">Lead Status</label>
                <input type="text" id="leadStatus" name="lead_status">
            </div>

            <div class="form-group form-col">
                <label for="estimatedCloseDate">Estimated Close Date</label>
                <input type="date" id="estimatedCloseDate" name="estimated_close_date">
            </div>
        </div>

        <button type="submit">Submit</button>
    </form>

</body>

</html>
<?php include('includes/footer.php'); ?>