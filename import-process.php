<?php
require_once __DIR__ . '/config/database.php';
include_once 'includes/lib/main_lib.php';
verify_user();
require 'vendor/autoload.php'; // PhpSpreadsheet library ko load karna (Excel file read/write ke liye)

use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\IOFactory; // IOFactory class use kar rahe hain file load karne ke liye


function detectTypeFromColumn($columnData, $sampleLimit = 10)
{
    $types = ['int' => 0, 'float' => 0, 'datetime' => 0, 'string' => 0];
    $count = 0;

    foreach ($columnData as $val) {
        $val = trim($val);
        if ($val === '') continue;

        if (is_numeric($val)) {
            if (strpos($val, '.') !== false) $types['float']++;
            else $types['int']++;
        } elseif (strtotime($val)) {
            $types['datetime']++;
        } else {
            $types['string']++;
        }

        $count++;
        if ($count >= $sampleLimit) break;
    }

    // Decide based on most matches
    arsort($types); // sort descending
    $topType = array_key_first($types);

    switch ($topType) {
        case 'int':
            return 'INT';
        case 'float':
            return 'FLOAT';
        case 'datetime':
            return 'DATETIME';
        default:
            return 'VARCHAR(255)';
    }
}

// echo '<pre>';
// print_r($_FILES);
// echo '</pre>';
// exit();
if (!empty($_FILES)) { // Check kar rahe hain ke 'import' button press hua hai ya nahi
    if (!empty($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) { // Check ke file upload hui hai aur koi error nahi aya

        $file_name = $_FILES['excel_file']['name']; // File ka asal naam hasil kar rahe hain
        $file_tmp = $_FILES['excel_file']['tmp_name']; // Temporary path hasil kar rahe hain jahan file server ne rakhi hai
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // File ka extension hasil kar ke lowercase mein convert kar rahe hain

        if (in_array($file_ext, ['xls', 'xlsx'])) { // Check kar rahe hain ke file ka extension Excel (.xls/.xlsx) hi hai

            $spreadsheet = IOFactory::load($file_tmp); // Excel file ko load kar rahe hain
            $sheet = $spreadsheet->getActiveSheet(); // Active sheet (jaise Sheet1) ko hasil kar rahe hain
            $rows = $sheet->toArray(); // Sheet ke tamam rows ko array mein convert kar rahe hain
            // echo '<pre>';
            // print_r($rows);
            // echo '</pre>';
            // exit();
            $header_row = '';
            $column_type = '';
            if (!empty($rows)) {
                $header_row = $rows[0];
                $column_type = $rows[1];
                $column_length = $rows[2];
            }


            // file column and data type
            $base_data = array();

            foreach ($header_row as $key => $value) {
                if (!empty($value)) {
                    // $base_data[str_replace(" ","_",strtolower(trim($value)))."_name"] = $column_type[$key];
                    // $base_data[str_replace(" ","_",strtolower(trim($value)))."_length"] = $column_length[$key];
                    $temporary_type = $column_type[$key] == 'string' ? 'VARCHAR' : $column_type[$key];
                    $temporary_type = $column_type[$key] == 'url' ? 'VARCHAR' : $temporary_type;
                    $temporary_type = $column_type[$key] == 'decimal' ? 'FLOAT' : $temporary_type;
                    $temporary_type = $column_type[$key] == 'number' ? 'MEDIUMINT' : $temporary_type;
                    $temporary_type = $column_type[$key] == 'text' ? 'BLOB' : $temporary_type;
                    $base_data[str_replace(" ", "_", strtolower(trim($value)))] = $temporary_type . "(" . $column_length[$key] . ")";
                }
            }

            // echo '<pre>base_data';
            // print_r($base_data);
            // echo '</pre>';
            // exit();

            $table = 'master_sheet';
            $sqltruncate = "DROP TABLE $table";
            $conn->query($sqltruncate);
            $createtable = "CREATE TABLE master_sheet (id INT AUTO_INCREMENT PRIMARY KEY)";
            $conn->query($createtable);
            // Execute a query to retrieve data from the table
            $query = "SELECT * FROM $table LIMIT 1";
            $result = $conn->query($query);

            // Check if the query was successful
            if ($result) {
                // Initialize an empty array to store column names and data types
                $columns = [];


                // Fetch field information for all columns
                $fields = $result->fetch_fields();
                // echo '<pre>fields';
                // print_r($fields);
                // echo '</pre>';
                // exit();
                // Define the type mapping
                $typeMapping = [
                    MYSQLI_TYPE_TINY      => 'TINYINT',
                    MYSQLI_TYPE_SHORT     => 'SMALLINT',
                    MYSQLI_TYPE_LONG      => 'INT',
                    MYSQLI_TYPE_INT24     => 'MEDIUMINT',
                    MYSQLI_TYPE_LONGLONG  => 'BIGINT',
                    MYSQLI_TYPE_DECIMAL   => 'DECIMAL',
                    MYSQLI_TYPE_NEWDECIMAL => 'DECIMAL',
                    MYSQLI_TYPE_FLOAT     => 'FLOAT',
                    MYSQLI_TYPE_DOUBLE    => 'DOUBLE',
                    MYSQLI_TYPE_DATE      => 'DATE',
                    MYSQLI_TYPE_TIME      => 'TIME',
                    MYSQLI_TYPE_DATETIME  => 'DATETIME',
                    MYSQLI_TYPE_TIMESTAMP => 'TIMESTAMP',
                    MYSQLI_TYPE_YEAR      => 'YEAR',
                    MYSQLI_TYPE_BIT       => 'BIT',
                    MYSQLI_TYPE_STRING    => 'CHAR',
                    MYSQLI_TYPE_VAR_STRING => 'VARCHAR',
                    MYSQLI_TYPE_BLOB      => 'BLOB',
                    MYSQLI_TYPE_JSON      => 'JSON',
                    // Add other mappings as needed
                ];


                foreach ($fields as $field) {
                    // echo '<pre>field';
                    // print_r($field);
                    // echo '</pre>';
                    // exit();
                    // Map the type to a human-readable format
                    $type = $typeMapping[$field->type] ?? 'UNKNOWN';

                    // Construct the SQL data type string
                    if ($type === 'VARCHAR' && $field->charsetnr > 0) {
                        $sqlType = "{$type}({$field->charsetnr})";
                    } elseif ($type === 'MEDIUMINT' && $field->length > 0) {
                        $sqlType = "{$type}({$field->length})";
                    } elseif ($type === 'FLOAT' && $field->length > 0) {
                        $sqlType = "{$type}({$field->length})";
                    } else {
                        $sqlType = "{$type}({$field->length})";
                    }
                    // Map each column name to its data type
                    $columns[$field->name] = $sqlType;
                }
                // echo '<pre>column';
                // print_r($columns);
                // echo '</pre>';
                // exit();
                // Output the associative array
                // print_r($columns);
                // exit;
            } else {
                echo "Error: " . $mysqli->error;
                exit;
            }


            // echo '<pre> check';
            // print_r($columns);
            // print_r($base_data);
            // echo '</pre>';
            //    exit();

            foreach ($base_data as $column_name => $column_type) {
                if (empty($column_name) || empty($column_type)) continue;
                $sql = "ALTER TABLE `$table` ADD `$column_name` $column_type";
                $conn->query($sql);
            }

            if (!empty($columns) and !empty($base_data)) {
                $differences = array_diff_assoc($base_data, $columns);

                if ($differences) {
                    foreach ($differences as $column_name => $column_type) {
                        if (empty($column_name) || empty($column_type)) {
                            echo "Skipped invalid column: $column_name with type: $column_type<br>";
                            continue;
                        }
                        $check_sql = "SHOW COLUMNS FROM `$table` LIKE '$column_name'";
                        $check_result = $conn->query($check_sql);

                        if ($check_result && $check_result->num_rows == 0) {
                            $sql = "ALTER TABLE `$table` ADD `$column_name` $column_type";
                            $conn->query($sql);
                        }
                    }
                }
            }



            if (!empty($columns) && !empty($base_data)) {
                // Only drop columns that don't exist in base_data
                foreach ($columns as $colName => $type) {
                    if ($colName === 'id') continue; // Keep primary key

                    if (!array_key_exists($colName, $base_data)) {
                        $sql = "ALTER TABLE `$table` DROP `$colName`";
                        if (!$conn->query($sql)) {
                            echo "Failed to drop $colName: " . $conn->error . "<br>";
                        }
                    }
                }
            }

            $sql = '';
            $column_names = '';
            $sql = "Insert into master_sheet ";
            $length = count($base_data);

            foreach ($base_data as $k => $data) {
                if (--$length == 0)
                    $column_names .= $k;
                else
                    $column_names .= $k . ",";
            }
            //---------------
            $dataRows = array_slice($rows, 3); // skip header, type, and length rows
            $dataRows = array_filter($dataRows, function ($row) {
                return !empty(array_filter($row)); // Remove fully empty rows
            });
            //--------------
            $columns = array_keys($base_data);
            // Insert new rows
            foreach ($dataRows as $index => $data) {

                $row = array_slice($data, 0, count($columns));
                $values = array_map(fn($v) => $v === null || $v === '' ? "NULL" : "'" . $conn->real_escape_string($v) . "'", $row);
                $values = str_replace([',','&'],['/','and'],$values);
                $sql = "INSERT INTO `$table` (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ")";
                // echo '<pre>';
                // print_r($sql);
                // echo '</pre>';
                // exit();
                $conn->query($sql);
            }
            //---------------


            //=========logging=============
            if (!$_SESSION) {
                session_start();
            }
            $user_name = isset($_SESSION['auth']['username']) ? $_SESSION['auth']['username']: 'Unknown';
            date_default_timezone_set('Asia/Karachi');

            // Collect names of newly added columns
            $added_columns = array_keys($differences);
            $column_names = implode(',', $added_columns);
            $col_count = count($columns);
            $file_name = $_FILES['excel_file']['name'];
            $date = date('Y-m-d');
            $datetime = date('Y-m-d H:i:s');

            $status_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM imports WHERE `file_name` = '$file_name' AND DATE(`datetime`) = '$date'");
            $status_data = mysqli_fetch_assoc($status_result);
            $status = $status_data['count'] + 1;

            $insert_import = "INSERT INTO imports (`file_name`,`imported_by`, `datetime`, `status`, `no_of_columns`, `column_name`)
                  VALUES ('$file_name', '$user_name', '$datetime', '$status', '$col_count', '$column_names')";

            mysqli_query($conn, $insert_import);
            
            //======================
            echo "<script>alert('Excel file  data imported successfully ');
            window.location.href='import.php';</script>"; // Success message show kar rahe hain
        } else {
            echo "<script>alert('Invalid file type. Only .xls or .xlsx files allowed');
            window.location.href='import.php';</script>"; // Agar file Excel nahi hai to error message
        }
    } else {
        echo "Please upload a valid Excel file."; // Agar file nahi mili ya error aaya to ye message show hoga
    }
}
?>
<!-- horizontal scroll table -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollWrapper = document.querySelector('.table-scroll-wrapper');

        if (scrollWrapper) {
            scrollWrapper.addEventListener('wheel', function(e) {
                if (scrollWrapper.scrollWidth > scrollWrapper.clientWidth) {
                    e.preventDefault();
                    scrollWrapper.scrollLeft += e.deltaY;
                }
            }, {
                passive: false
            });
        }
    });
</script>