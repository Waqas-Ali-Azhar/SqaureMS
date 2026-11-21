<?php
include_once 'includes/header.php';
include 'includes/topbar.php';
include 'includes/sidebar.php';

$sql = "SELECT * FROM plugins";
$result = $conn->query($sql);

echo '<div class="container-fluid">';
echo '<div class="row">';
echo '<div class="col-sm-12">';
echo '<div class="content-wrapper">';
echo '<div class="table-wrapper">';
echo '<div class="table-space">';
echo '<div class="table-scroll-wrapper">';
echo '<form method="POST" class="form-filter">';

if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    echo "<table style='border-collapse: collapse; width: 100%; border: 1px solid #ddd;' class='filter-table'>";
    
    // Print table headers
    echo '<thead><tr>';
    foreach (array_keys($rows[0]) as $column) {
        $column_display = str_replace('_', ' ', $column);
        echo "<th style='border: 1px solid #ddd; padding: 8px;'>$column_display</th>";
    }
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Actions</th>";
    echo '</tr></thead><tbody>';

    // Print table rows
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>$cell</td>";
        }

        // Add Action buttons
        $id = $row['id'];
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>
                <a href='edit-plugin.php?id=$id' class='btn btn-sm btn-primary'>Edit</a>
                <a href='edit-plugin.php?id=$id&delete=1' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this plugin?');\">Delete</a>
              </td>";
        echo '</tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No data found.</p>';
}

echo '</form>';
echo '</div>'; 
echo '</div>';
echo '</div>'; 
echo '</div>'; 
echo '</div>'; 
echo '</div>'; 
echo '</div>'; 
?>

<?php include('includes/footer.php'); ?>
