<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

require_once __DIR__ . '/config/database.php';

$master = array();
$general = array();
$specific = array();
$cleanedProjectRows = [];
$sslStatusRow = [];

function insertIfNotExistsByName($conn, $table, $name)
{
    $name = trim($name);
    $name = str_replace([',', '&'], ['/', 'and'], $name);
    if ($name === '') return false;
    $subname = '';
    if (strpos($name, '/') !== false) {
        $parts = explode('/', $name);
        $name = trim($parts[0]);
        $subname = trim(end($parts));
    }
    $safeName = $conn->real_escape_string($name);
    $slug = strtolower(str_replace(' ', '-', $safeName));
    $url = 'http://www.' . $slug . '.com';

    $check = $conn->query("SELECT 1 FROM `$table` WHERE name = '$safeName' LIMIT 1");

    if ($check && $check->num_rows === 0) {
        if ($table === 'general_data') {
            $conn->query("INSERT INTO `$table` (Name, Slug, URL, subname) VALUES ('$safeName', '$slug', '$url', '$subname')");
        } else {
            $conn->query("INSERT INTO `$table` (Name, Slug, URL) VALUES ('$safeName', '$slug', '$url')");
        }
    }
    return false;
}

function insertIfNotExistsSpecific($conn, $table, $name)
{
    $name = trim($name);
    if ($name === '') return false;
    $name = str_replace([',', '&'], ['/', 'and'], $name);
    $safeName = $conn->real_escape_string($name);
    $slug = strtolower(str_replace(' ', '-', $safeName));
    $url = $slug;
    $check = $conn->query("SELECT 1 FROM `$table` WHERE name = '$safeName' LIMIT 1");

    if ($check && $check->num_rows === 0) {
        $conn->query("INSERT IGNORE INTO `$table` (Name, Slug, URL) VALUES ('$safeName', '$slug','$url')");
        return true;
    }
    return false;
}

if (!empty($_FILES) && !empty($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
    $file_name = $_FILES['excel_file']['name'];
    $file_tmp = $_FILES['excel_file']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_ext, ['xls', 'xlsx'])) {
        $spreadsheet = IOFactory::load($file_tmp);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $header_row = $rows[0];
        $column_type = $rows[1];
        $column_length = $rows[2];
        //---------- for store columns base on name instead of index-----
        $headerMap = [];
        foreach ($header_row as $index => $headerName) {
            $cleaned = strtolower(str_replace(' ', '_', trim($headerName))); // Normalize header
            if ($cleaned !== '') {
                $headerMap[$cleaned] = $index;
            }
        }
        // $headerMap['testurl'] // will give the correct column index for 'testurl'
        //----------

        $base_data = [];
        foreach ($header_row as $key => $value) {

            if (!empty($value)) {
                $temporary_type = match ($column_type[$key]) {
                    'string', 'url' => 'VARCHAR',
                    'decimal' => 'FLOAT',
                    'number' => 'MEDIUMINT',
                    default => $column_type[$key]
                };
                $column_name = str_replace(" ", "_", strtolower(trim($value)));
                if (!in_array($column_name, array('master', 'general', 'specifics')))
                    $base_data[$column_name] = $temporary_type . "(" . $column_length[$key] . ")";
            }
        }

        $table = 'new_projects';
        $conn->query("DROP TABLE IF EXISTS $table");
        $conn->query("CREATE TABLE $table (id INT AUTO_INCREMENT PRIMARY KEY)");

        foreach ($base_data as $column => $type) {
            $conn->query("ALTER TABLE `$table` ADD `$column` $type");
        }

        $dataRows = array_slice($rows, 3);
        // truncate new_columns and builder_support tables
        $truncateColumns = $conn->query("TRUNCATE new_columns");
        $truncateBuilderSupport = $conn->query("TRUNCATE builder_support");
        $truncateAllowedUrls = $conn->query("TRUNCATE allowed_urls");
        foreach ($dataRows as $row) {
            $row = array_map(fn($v) => trim((string)$v), $row);
            if (empty(array_filter($row))) continue;

            if (isset($row[1]) && !in_array($row[1], $master)) $master[] = $row[1];
            if (isset($row[2]) && !in_array($row[2], $general)) $general[] = $row[2];
            if (isset($row[3]) && !in_array($row[3], $specific)) $specific[] = $row[3];
            unset($row[1]);
            unset($row[2]);
            unset($row[3]);
            //-----------------ssl-------------
            $articleNumber = $conn->real_escape_string($row[$headerMap['article_number']] ?? '');
            if ($articleNumber !== '') {
                $checkSSL = $conn->query("SELECT 1 FROM ssl_status WHERE article_number = '$articleNumber' LIMIT 1");
                if ($checkSSL && $checkSSL->num_rows === 0) {
                    $conn->query("INSERT INTO ssl_status (article_number, ssl_status) VALUES ('$articleNumber', 'No')");
                }
            }
            //insert into new_columns
            $articleNumber = $conn->real_escape_string($row[$headerMap['article_number']] ?? '');
            $publicView = $conn->real_escape_string($row[24]);
            $returns = $conn->real_escape_string($row[25]);
            $pluginInstalled = $conn->real_escape_string($row[26]);
            $cache_status = $conn->real_escape_string($row[$headerMap['cache_status']] ?? '');
            $multiDemos = $conn->real_escape_string($row[28]);
            $multiCategory = $conn->real_escape_string($row[29]);
            $customCoded = $conn->real_escape_string($row[31]);
            $variationProducts = $conn->real_escape_string($row[32]);
            $ServiceForms = $conn->real_escape_string($row[33]);
            $builderSupport = $conn->real_escape_string($row[30]);
            $AllowedUrl = (int)$row[46];
            //new_columns insert
            $InsertColumns = "INSERT INTO new_columns(article_number , public_view, returns, plugin_installed, cache_status, multi_demos, multi_category, custom_coded, variation_product, service_forms ) values ('$articleNumber','$publicView','$returns','$pluginInstalled','$cache_status','$multiDemos','$multiCategory','$customCoded','$variationProducts','$ServiceForms')";
            $conn->query($InsertColumns);
            //buildersupport
            $InsertBuilderSupport = "INSERT INTO builder_support(article_number,builder_support) values ('$articleNumber','$builderSupport')";
            $conn->query($InsertBuilderSupport);
            //allowed url
            $InsertIntoAllowdUrls = "INSERT INTO allowed_urls(article_number, allowed_url) VALUES($articleNumber, $AllowedUrl)";
            $conn->query($InsertIntoAllowdUrls);
            //cache_status
            if ($articleNumber !== '') {
                $checkCache = $conn->query("SELECT 1 FROM cache_status WHERE article_number = '$articleNumber' LIMIT 1");
                if ($checkCache && $checkCache->num_rows === 0) {
                    $conn->query("INSERT INTO cache_status(article_number, cache_status) VALUES ('$articleNumber', '$cache_status')");
                }
            }


            $row = array_slice($row, 0, count($base_data));
            $row = array_pad($row, count($base_data), null);

            $cleanedProjectRows[] = $row;
        }


        $conn->query("SET foreign_key_checks = 0");
        foreach (['master_data', 'general_data', 'specific_data', 'new_projects', 'specific_project'] as $table_name) {
            $conn->query("TRUNCATE TABLE $table_name");
        }
        $conn->query("SET foreign_key_checks = 1");

        foreach ($master as $value) insertIfNotExistsByName($conn, 'master_data', $value);
        foreach ($general as $value) insertIfNotExistsByName($conn, 'general_data', $value);
        foreach ($specific as $value) insertIfNotExistsSpecific($conn, 'specific_data', $value);

        $columns_result = $conn->query("SHOW COLUMNS FROM `new_projects`");
        $columns = [];
        while ($row = $columns_result->fetch_assoc()) {
            if ($row['Field'] != 'id') $columns[] = $row['Field'];
        }
        foreach ($cleanedProjectRows as $rowValues) {
            if (count($rowValues) !== count($columns)) continue;

            $escapedValues = array_map(function ($val, $i) use ($conn, $columns) {
                $colName = strtolower($columns[$i]);

                // Validate price as numeric
                if ($colName === 'price' && !is_numeric($val)) {
                    $val = 0;
                }

                return (is_null($val) || $val === '' || strtolower($val) === 'null')
                    ? "NULL"
                    : "'" . $conn->real_escape_string($val) . "'";
            }, $rowValues, array_keys($rowValues));

            $columnsList = implode(',', $columns);
            $valuesList = implode(',', $escapedValues);
            $conn->query("INSERT INTO `new_projects` ($columnsList) VALUES ($valuesList)");
            // if ($columnsList === 'ssl'){
            //     $query_ssl = $conn->query("INSERT INTO ssl_status");
            // }

        }

        $specific_map = [];
        $result = $conn->query("SELECT id, name FROM specific_data");
        while ($row = $result->fetch_assoc()) {
            $name = strtolower(trim($row['name']));
            $specific_map[$name] = $row['id'];
        }

        $projects = $conn->query("SELECT id FROM new_projects");
        $project_ids = [];
        while ($row = $projects->fetch_assoc()) {
            $project_ids[] = $row['id'];
        }

        foreach ($dataRows as $i => $row) {
            if (!isset($row[3]) || trim($row[3]) === '') continue;

            $specific_name = strtolower(trim(str_replace(['&', ','], ['and', '/'], $row[3])));
            if (isset($specific_map[$specific_name]) && isset($project_ids[$i])) {
                $sid = $specific_map[$specific_name];
                $pid = $project_ids[$i];

                $check = $conn->query("SELECT 1 FROM specific_project WHERE specific_id = $sid AND project_id = $pid");
                if ($check->num_rows == 0) {
                    $conn->query("INSERT INTO specific_project (specific_id, project_id) VALUES ($sid, $pid)");
                }
            }
        }

        echo "<script>alert('Excel file data imported successfully'); window.location.href='import_data.php';</script>";
    } else {
        echo "<script>alert('Invalid file type. Only .xls or .xlsx files allowed'); window.location.href='import_data.php';</script>";
    }
} else {
    echo "Please upload a valid Excel file.";
}
