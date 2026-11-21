<?php
require_once __DIR__ . '/config/database.php';

$lead_id = $_POST['lead_id'];
$developer_id = $_POST['developer_id'];

// Get developer name (required since you store by name)
$result = mysqli_query($conn, "SELECT full_name FROM users WHERE id = $developer_id");
$row = mysqli_fetch_assoc($result);
$developer_name = $row['full_name'];

// Now delete the assignment
$deleteQuery = "DELETE FROM assigned_to_developer WHERE lead_id = $lead_id AND developer_name = '" . mysqli_real_escape_string($conn, $developer_name) . "'";
if (mysqli_query($conn, $deleteQuery)) {
      $updateQuery = "
                    UPDATE leads 
                    SET last_updated = NOW(), lead_assigned_date = NULL 
                    WHERE id = $lead_id
                ";
                $conn->query($updateQuery);
    echo "Developer unassigned successfully.";
} else {
    echo "Failed to unassign developer.";
}
?>
