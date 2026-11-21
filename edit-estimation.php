<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

$estimation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$estimationData = [];
if ($estimation_id > 0) {
    $query = "SELECT * FROM estimation WHERE id = $estimation_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $estimationData = $result->fetch_assoc();
    } else {
        echo "<script>alert('Estimation not found.'); window.location.href='estimation.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid ID.'); window.location.href='estimation.php';</script>";
    exit;
}

// Input type mapper based on MySQL type
function getInputType($mysqlType)
{
    $type = strtolower($mysqlType);
    if (strpos($type, 'int') !== false) {
        return 'number';
    } elseif (strpos($type, 'decimal') !== false || strpos($type, 'float') !== false || strpos($type, 'double') !== false) {
        return 'number';
    } elseif (strpos($type, 'datetime') !== false) {
        return 'datetime-local';
    } elseif (strpos($type, 'varchar') !== false || strpos($type, 'text') !== false) {
        return 'text';
    } elseif (strpos($type, 'date') !== false) {
        return 'date';
    } elseif (strpos($type, 'time') !== false) {
        return 'time';
    } else {
        return 'text';
    }
}

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST !== '') {
    $updateFields = [];
    foreach ($_POST as $key => $value) {
        if ($key === 'id') continue;

        $mysqlType = $schema[$key] ?? '';
        $trimmed = trim($value);

        if ($trimmed === '') {
            $updateFields[] = "`$key` = NULL";
            continue;
        }
        if (isset($schema[$key]) && strpos(strtolower($schema[$key]), 'date') !== false) {
            $value = date('Y-m-d', strtotime($value));
        }

        $value = mysqli_real_escape_string($conn, $value);
        $updateFields[] = "`$key` = '$value'";
    }

    $updateSQL = "UPDATE estimation SET " . implode(', ', $updateFields) . " WHERE id = $estimation_id";
    if (mysqli_query($conn, $updateSQL)) {
        echo "<script>alert('Estimation updated successfully!'); window.location.href='estimations.php';</script>";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
// Handle deletion via ?delete=1
if (isset($_GET['delete']) && $_GET['delete'] == 1 && $estimation_id > 0) {
    $deleteQuery = "DELETE FROM estimation WHERE id = $estimation_id";
    if (mysqli_query($conn, $deleteQuery)) {
        echo "<script>alert('Estimation deleted successfully.'); window.location.href='estimations.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting estimation: " . mysqli_error($conn) . "');</script>";
    }
}

// Get table schema
$schema = [];
$database = mysqli_fetch_assoc(mysqli_query($conn, "SELECT DATABASE()"))["DATABASE()"];
$schemaquery = "
    SELECT COLUMN_NAME, COLUMN_TYPE 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE table_schema = '$database' AND table_name = 'estimation'
";
$schemaresult = $conn->query($schemaquery);
if ($schemaresult && $schemaresult->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($schemaresult)) {
        $schema[$row['COLUMN_NAME']] = $row['COLUMN_TYPE'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Estimation</title>
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
    <form action="edit-estimation.php?id=<?php echo $estimation_id; ?>" method="POST" class="project-form">
        <h2>Edit Estimation (ID: <?= $estimation_id ?>)</h2>

        <?php
        $count = 0;
        foreach ($estimationData as $key => $value) {
            $mysqlType = $schema[$key] ?? 'varchar';
            $inputType = getInputType($mysqlType);

            if ($count % 2 === 0) {
                echo '<div class="form-row">';
            }

            echo '<div class="form-group form-col">';
            $readonlyFields = ['developer_name', 'business_manager_name']; // list of readonly fields
            $isReadonly = in_array(strtolower($key), $readonlyFields) ? ' readonly' : '';
            echo '<label for="' . $key . '">' . ucwords(str_replace("_", " ", $key)) . '</label>';
            echo '<input type="' . $inputType . '" id="' . $key . '" name="' . $key . '" value="' . $value . '" '.$isReadonly.'>';
            echo '</div>';

            $count++;
            if ($count % 2 === 0) {
                echo '</div>';
            }
        }
        if ($count % 2 !== 0) {
            echo '</div>';
        }
        ?>
        <button type="submit">Update Estimation</button>
    </form>
</body>

</html>
<?php include('includes/footer.php'); ?>