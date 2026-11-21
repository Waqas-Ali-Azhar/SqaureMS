<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
$dataValue = [];
$ch = curl_init();
$url = "https://adminhub.squarebrackets.io/feed-360/api-v1/master";
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if ($response === false) {
    die('Curl error: ' . curl_error($ch));
}
curl_close($ch);

$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('JSON decode error: ' . json_last_error_msg());
}
foreach ($data as $item) {
    if (isset($item['Name'])) {
        $dataValue[] = $item['Name'];
    }
}
//brands for dropdown
$brandQuery = "SELECT id, brand FROM brands";
$brandResult = $conn->query($brandQuery);

//------------------------
$select = "SELECT service_name FROM additional_services";
$result = $conn->query($select);
//insert query form
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST !== '') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $specifics = mysqli_real_escape_string($conn, $_POST['specifics']);
    $servicesArray = $_POST['services'];
    $services = mysqli_real_escape_string($conn, implode(',', $servicesArray));
    $budget = mysqli_real_escape_string($conn, $_POST['budget']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $brand = $_POST['brand'];

    //  Insert into clients table
    $insertClientQuery = "INSERT INTO clients (name, phone_number, email, country, city, address)
                          VALUES ('$name', '$phone', '$email', '$country', '$city', '$address')";

    if (mysqli_query($conn, $insertClientQuery)) {
        $client_id = mysqli_insert_id($conn);
        $createdAt = $conn->query("UPDATE clients SET created_date = NOW() WHERE id = $client_id");
        // Insert into leads table with the client_id
        $insertLeadQuery = "INSERT INTO leads (client_id, name, email, phone, country, city, address, specifics, services, budget, details, brand)
                            VALUES ('$client_id', '$name', '$email', '$phone', '$country', '$city', '$address', '$specifics', '$services', '$budget', '$details','$brand')";

        if (mysqli_query($conn, $insertLeadQuery)) {
            $leadId = mysqli_insert_id($conn);
            $assignedDAte = $conn->query("UPDATE leads SET lead_creation_date = NOW() WHERE id = $leadId");
            echo "<script>alert('Lead added successfully!'); window.location.href='add_lead.php';</script>";
        } else {
            echo "Error inserting lead: " . mysqli_error($conn);
        }
    } else {
        echo "Error inserting client: " . mysqli_error($conn);
    }
}
//---
?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery (Required by Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
    <div class="container mt-5">
        <div class="content-wrapper">
            <div class="card shadow">
                <div class="card-header">
                    <h4 class="mb-0">Add Lead</h4>
                </div>

                <div class="card-body">
                    <form action="/add_lead.php" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="country">Country</label>
                                <input type="text" id="country" name="country" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="city">City</label>
                                <input type="text" id="city" name="city" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="address">Address</label>
                                <input type="text" id="address" name="address" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label" for="specifics">Project Type</label>
                                <select class="form-select" name="specifics" id="specifics" required>
                                    <option value="" disabled selected>Select Project Type</option>
                                    <?php foreach ($dataValue as $val): ?>
                                        <option value="<?= htmlspecialchars($val) ?>"><?= htmlspecialchars($val) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="brand_id">Brand</label>
                                <select class="form-select" name="brand" id="brand" required>
                                    <option value="" disabled selected>Select Brand</option>
                                    <?php
                                    if ($brandResult && $brandResult->num_rows > 0) {
                                        while ($brand = $brandResult->fetch_assoc()) {
                                            echo "<option value='{$brand['id']}'>" . htmlspecialchars($brand['brand']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h5>Project Details</h5>

                        <div class=" mb-3">
                            <label class="form-label" for="services">Additional Services</label>
                            <select id="services" name="services[]" class="form-select" multiple>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $service = htmlspecialchars($row['service_name']);
                                        echo "<option value=\"$service\">$service</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="budget">Project Budget</label>
                            <input type="text" id="budget" name="budget" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="details">Project Detail</label>
                            <textarea id="details" name="details" rows="4" class="form-control"></textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#services').select2({
                placeholder: "Select additional services",
                allowClear: true
            });
        });
    </script>

</body>

</html>
<?php include('includes/footer.php'); ?>