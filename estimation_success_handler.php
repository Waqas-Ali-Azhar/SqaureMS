<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $lead_id = $_POST['lead_id'];
    $client_id = $_POST['client_id'];
    $estimation_id = $_POST['id'];

    $estimationTitle = $_POST['estimation_title'];
    $contract_id ='';
    $project_id = '';
    // Start transaction
    mysqli_begin_transaction($conn);

    // Insert into contracts
    $sql_contract = "INSERT INTO contracts (lead_id, title) 
                     VALUES ('$lead_id', '$estimationTitle')";

    if (mysqli_query($conn, $sql_contract)) {
        $contract_id = mysqli_insert_id($conn);
    } else {
        mysqli_rollback($conn);
        die("Error inserting into contracts: " . mysqli_error($conn));
    }

    // Insert into projects
    $sql_project = "INSERT INTO projects (lead_id, project_title) 
                    VALUES ('$lead_id', '$estimationTitle')";

    if (mysqli_query($conn, $sql_project)) {
        $project_id = mysqli_insert_id($conn);
    } else {
        mysqli_rollback($conn);
        die("Error inserting into projects: " . mysqli_error($conn));
    }

    // Update estimations table with contract_id and project_id
    $sql_update = "UPDATE estimation
                   SET contract_id = '$contract_id', project_id = '$project_id' 
                   WHERE lead_id = '$lead_id'";

    if (mysqli_query($conn, $sql_update)) {
        mysqli_commit($conn);
        echo "Success: Data inserted and estimations updated.<br>";
        echo "Contract ID: $contract_id<br>";
        echo "Project ID: $project_id<br>";
    } else {
        mysqli_rollback($conn);
        die("Error updating estimations: " . mysqli_error($conn));
    }


    //------------insert ids into sale table
    $insertIDs = "INSERT INTO sale (lead_id, estimation_id, contract_id, project_id, client_id) VALUES ($lead_id, $estimation_id, $contract_id,$project_id,$client_id )";
    $resultInsertIDs = $conn ->query($insertIDs);
    if(!$resultInsertIDs){
        echo "Failed IDs store!";
    }

    mysqli_close($conn);

} else {
    echo "Invalid request.";
}
?>
