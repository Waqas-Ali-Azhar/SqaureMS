<?php
include_once 'includes/header.php';
include 'includes/topbar.php';
include 'includes/sidebar.php';

$sql = "SELECT* FROM roles";
$result = $conn->query($sql);
echo '<div class="container-fluid">';
echo '<div class="row">';
echo '<div class="col-sm-12">';
echo '<div class="content-wrapper">';
echo '<div class="table-wrapper" style="padding:15px 15px;">';
echo '<a href="/add-roles.php">Add New Role</a>';
if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    echo '<div class="table-scroll-wrapper">';
    echo  "<form id='data-form' name='data-form' method='POST' class='form-filter'>";
    echo "<table style='border-collapse: collapse; width: 100%; border: 1px solid #ddd;' class='filter-table'>";

    // Print table headers from first row
    echo '<thead><tr>';
    foreach (array_keys($rows[0]) as $column) {
        $column_display = str_replace('_', ' ', $column);
        echo "<th style='border: 1px solid #ddd; padding: 8px;'>$column_display</th>";
    }
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Actions</th>";
    echo '</tr></thead><tbody>';

    // Print all rows
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($row as $value) {
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>$value</td>";
        }

        $id = $row['id']; // Adjust if your primary key is named differently
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>
                <a href='edit_role.php?id=$id'><span style='color:green;'>Edit</span></a> |
                <a href='delete_role.php?id=$id' onclick=\"return confirm('Are you sure you want to delete this role?');\"><span style='color:red;'>Delete</span></a>
              </td>";
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</form>';
    echo '</div';
} else {
    echo '<p>No roles found.</p>';
}

echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
?>
<script>
   document.addEventListener('DOMContentLoaded', function() {
        const scrollWrapper = document.querySelector('.table-scroll-wrapper');
        const tbody = scrollWrapper.querySelector('tbody');
        const tableWrapper = document.querySelector('.table-wrapper');
        // Horizontal scroll on table ONLY
        if (scrollWrapper && tbody) {
            tbody.addEventListener('wheel', function(e) {
                const canScroll = scrollWrapper.scrollWidth > scrollWrapper.clientWidth;
                if (canScroll) {
                    e.preventDefault();
                    scrollWrapper.scrollLeft += e.deltaY;
                    e.stopPropagation();
                }
            }, {
                passive: false
            });
        }

        if (tableWrapper) {
            tableWrapper.addEventListener('wheel', function(e) {
                const isInsideSideList = e.target.closest('.side-list');
                //&& !e.target.parentElement.parentElement.classList.contains('ulList')
                if (scrollWrapper.scrollHeight > scrollWrapper.clientHeight && !(e.target.closest('.ulList')) && !isInsideSideList) { // prevent from event trigger on side list
                    e.preventDefault();
                    scrollWrapper.scrollTop += e.deltaY;
                }
            }, {
                passive: false
            });
        }
    });

</script>

<?php include('includes/footer.php'); ?>
