<?php
require_once __DIR__ . '/config/database.php';

$lead_id = $_POST['lead_id'];
$business_manager_id = $_POST['business_manager_id'];

// Get business manager name
$result = mysqli_query($conn, "SELECT full_name FROM users WHERE id = $business_manager_id");
$row = mysqli_fetch_assoc($result);
$bm_name = $row['full_name'];

// Delete from assigned_to_business_manager
$deleteQuery = "DELETE FROM assigned_to_business_manager WHERE lead_id = $lead_id AND business_manager_name = '" . mysqli_real_escape_string($conn, $bm_name) . "'";
if (mysqli_query($conn, $deleteQuery)) {
      $updateQuery = "
                    UPDATE leads 
                    SET last_updated = NOW(), lead_assigned_date = NULL 
                    WHERE id = $lead_id
                ";
                $conn->query($updateQuery);
    echo "Business Manager unassigned successfully.";
} else {
    echo "Failed to unassign Business Manager.";
}
?>
