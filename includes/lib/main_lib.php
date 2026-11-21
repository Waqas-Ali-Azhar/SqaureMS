<?php


function verify_user()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Safe to start
    }

    if (!empty($_SESSION) and !empty($_SESSION['auth'])) {


        if ($_SESSION['auth']['last_active']) {
            $current_time = time();
            if ($_SESSION['auth']['logout_time'] > $current_time) {
                $_SESSION['auth']['last_active'] = time();
                $_SESSION['auth']['logout_time'] = strtotime("+25 minutes", time());
                return true;
            }
        }
    }

    session_destroy();
    header("Location: form.php");
    exit;
}
//==============================
function add_client($db, $table, $data){
    global $conn;
    if (!empty($data)) {
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_map(function ($val) use ($conn) {
            return "'" . $conn->real_escape_string($val) . "'";
        }, array_values($data)));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        if ($conn->query($sql)) {
            if ($conn->query($sql)) {
                return $conn->insert_id;
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
}
function add_lead($db, $table, $data){
    global $conn;
    if (!empty($data)) {
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_map(function ($val) use ($conn) {
            return "'" . $conn->real_escape_string($val) . "'";
        }, array_values($data)));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        if ($conn->query($sql)) {
            if ($conn->query($sql)) {
                return $conn->insert_id;
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
}
//-----------functions for view_sheet td selects----
function isEditableCacheStatusColumn($colName) {
    return str_starts_with($colName, 'cache_') ||
           str_starts_with($colName, 'selected_') ||
           str_starts_with($colName, 'optimization_') ||
           str_starts_with($colName, 'image_optimization_') ||
           str_starts_with($colName, 'cdn_') ||
           str_starts_with($colName, 'aggregation_status_') ||
           $colName === 'cloudflare_status';
}

function shouldSkipColumn($colName) {
    return stripos($colName, 'plugin') !== false || stripos($colName, 'comments') !== false;
}

function isCommentColumn($colName) {
    return stripos($colName, 'comments') !== false;
}
function isMultiselectPluginColumn($colName) {
    $pluginColumns = ['cache_plugin', 'cdn_plugin', 'optimization_plugin', 'image_optimization_plugin'];
    return in_array($colName, $pluginColumns);
}
function isSelectValue($colName) {
    $pluginColumns = ['variation_products'];
    return in_array($colName, $pluginColumns);
}

function columnsWithSelectYesNo($colName) {
    $selectColumns = ['ssl_status', 'public_view', 'returns', 'plugin_installed', 'multi_demos', 'multi_category', 'custom_coded', 'variation_product', 'service_forms']
;
    return in_array($colName, $selectColumns);
}

//--------------end-----------------------------------