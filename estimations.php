<style>
    .cell-display {
        display: block;
        width: 100%;
        min-height: 28px;
        padding: 4px;
        cursor: pointer;
    }

    .cell-editor {
        width: 100%;
        box-sizing: border-box;
    }
</style>

<?php
include_once 'includes/header.php';
include 'includes/topbar.php';
include 'includes/sidebar.php';
//-----------------------------

$editableCols = ['estimation1_hours', 'estimation2_hours', 'estimation3_hours', 'estimation1_notes', 'estimation2_notes', 'estimation3_notes', 'status_by_developer', 'status_by_business_manager', 'lead_status'];

//---------------------------
$statuseQuery = "SELECT name FROM status";
$statusResult = $conn->query($statuseQuery);

$statusOptions = [];
while ($row = $statusResult->fetch_assoc()) {
    $statusOptions[] = $row['name'];
}
//------------------------------
$sql = "SELECT * FROM estimation";
$result = $conn->query($sql);
echo '<div class="container-fluid">';
echo '<div class="row">';
echo '<div class="col-sm-12">';
echo '<div class="content-wrapper">';
echo '<div class="table-wrapper" >';
echo '<div class="table-space ">';
echo '<div class="table-scroll-wrapper">';
echo '<form method="POST"  class="form-filter">';
if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $id = '';

    echo "<table style='border-collapse: collapse; width: 100%; border: 1px solid #ddd;' class='filter-table'>";

    // Print table headers from first row
    echo '<thead><tr>';
    foreach (array_keys($rows[0]) as $column) {
        $column_display = str_replace('_', ' ', $column);
        echo "<th style='border: 1px solid #ddd; padding: 8px;'>$column_display</th>";
    }
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Actions</th>";
    echo '</tr></thead><tbody>';
    foreach ($rows as $row) {
        $id = $row['id'];

        $project_id = $row['project_id'];
        echo '<tr>';
        foreach ($row as $key => $cell) {
            $safeValue = htmlspecialchars($cell ?? '');
            if ($key === 'id') {
                $link = 'estimation-details.php?id=' . urlencode((string) ($id ?? '')) . '&project_id=' . urlencode((string) ($project_id ?? ''));
                echo "<td style='border: 1px solid #ddd; padding: 8px;'><a href ='{$link}'>{$safeValue}</a></td>";
            } elseif (in_array($key, $editableCols)) {
                echo "<td class='editable-cell' data-column='{$key}' data-row-id='{$row['id']}'>";
                echo "<div class='cell-display'>{$safeValue}</div>";

                // Example: assumes $statusOptions is an array of strings like ['New', 'In Progress', 'Done']
                if (str_contains($key, 'status')) {
                    echo "<select class='cell-editor' style='display:none; width:100%;'>";
                    echo "<option value='' disabled " . (($safeValue === '' || $safeValue === NULL) ? 'selected' : '') . ">Select Status</option>";
                    foreach ($statusOptions as $option) {
                        $selected = ($option === $safeValue) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($option, ENT_QUOTES) . "' $selected>$option</option>";
                    }
                    echo "</select>";
                } elseif (str_contains($key, 'notes')) {
                    echo "<textarea class='cell-editor' style='display:none; width:100%;'>{$safeValue}</textarea>";
                } else {
                    echo "<input type='number' class='cell-editor' style='display:none; width:100%;' value='{$safeValue}'>";
                }

                echo "</td>";
            } else {
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$safeValue}</td>";
            }
        }

        // Add Action buttons
        // $id = $row['id'];
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>
            <a href='edit-estimation.php?id=$id' class='btn btn-sm btn-primary'>Edit</a>
            <a href='edit-estimation.php?id=$id&delete=1' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this estimation?');\">Delete</a>
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
<script>
    //inline editing
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.editable-cell').forEach(cell => {
            const displayDiv = cell.querySelector('.cell-display');
            const editor = cell.querySelector('.cell-editor');

            displayDiv.addEventListener('click', () => {
                displayDiv.style.display = 'none';
                editor.style.display = 'block';
                editor.focus();
            });

            // Save on blur
            editor.addEventListener('blur', () => {
                const newValue = editor.value;
                const column = cell.dataset.column;
                const rowId = cell.dataset.rowId;

                // Update UI immediately
                displayDiv.textContent = newValue;
                displayDiv.style.display = 'block';
                editor.style.display = 'none';

                // Send AJAX request to save
                fetch('update_estimation_field.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${encodeURIComponent(rowId)}&column=${encodeURIComponent(column)}&value=${encodeURIComponent(newValue)}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log('Update response:', data);
                    });
            });
        });
    });
    // --- OTHER EXISTING SCROLL AND COLUMN LIST LOGIC (Keep these as they were) ---
    const scrollWrapper = document.querySelector('.table-scroll-wrapper');
    const tbody = scrollWrapper ? scrollWrapper.querySelector('tbody') : null;
    const tableWrapper = document.querySelector('.table-wrapper');

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

    if (tableWrapper && scrollWrapper) { // Check scrollWrapper existence
        tableWrapper.addEventListener('wheel', function(e) {
            const isInsideSideList = e.target.closest('.side-list');
            if (scrollWrapper.scrollHeight > scrollWrapper.clientHeight && !(e.target.closest('.ulList')) && !isInsideSideList) {
                e.preventDefault();
                scrollWrapper.scrollTop += e.deltaY;
            }
        }, {
            passive: false
        });
    }
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="/assets/dist/js/estimation.js"></script>
<?php include('includes/footer.php'); ?>