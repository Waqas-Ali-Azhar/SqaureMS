<?php
include_once 'includes/header.php';
include 'includes/topbar.php';
include 'includes/sidebar.php';

if (isset($_GET['empty_data']) && $_GET['empty_data'] == 1) {
    mysqli_query($conn, "DELETE FROM specific_data");
    mysqli_query($conn, "DELETE FROM general_data");
    mysqli_query($conn, "DELETE FROM master_data");

    echo "<script>
        alert('All tables emptied successfully.');
        window.location.href = 'specific.php';
    </script>";
    exit;
}
$query = "SELECT * FROM specific_data ORDER BY id ASC";
$result = mysqli_query($conn, $query);
?>

<?php

$base_url = 'specific/';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <div class="add-new-btn">
                        <button><a href="specific-form.php">Add New</a></button>
                    </div>
                </div>
                <div class="col-sm-6" style='display:flex; justify-content:flex-end; align-items:flex-end;'>
                    <div class="emptytable">
                        <a href="?empty_data=1" onclick="return confirm('Are you sure you want to delete all data?');" title="Empty Data">
                            <i class="fas fa-trash-alt"></i> Empty Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-wrapper" style="display: flex;">
        <div class="table-scroll-wrapper">
            <table class="dashboard-table filter-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>URL</th>
                        <th>Content URL</th>
                        <th>Thumbnail</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['slug'] ?></td>
                            <td>
                                <a href="<?= $row['url'] ?>" target="_blank">
                                    <?= $row['url'] . '<br>' ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= '/specific/' . $row['url'] ?>" target="_blank">
                                    <?= $row['url'] . '<br>' ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($row['thumbnail'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['thumbnail']) ?>" alt="Thumbnail" width="80">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="specific-form.php?id=<?= $row['id'] ?>">Edit</a> |
                                <a href="specific-delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
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
<?php
include('includes/footer.php');
?>