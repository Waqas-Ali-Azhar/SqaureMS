<?php
include_once('includes/header.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');
$sql = ' SELECT * FROM  users ';
$result = $conn->query($sql);
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="content-wrapper">
                <div class="table-wrapper specific-wrapper">
                    <div class="table-space ">
                        <div class="table-scroll-wrapper">
                            <form id='data-form' name='data-form' method='POST' class='form-filter'>
                                <table style="border-collapse: collapse;" class="filter-table">
                                    <thead>
                                        <tr>
                                            <?php
                                            $fields = $result->fetch_fields();
                                            foreach ($fields as $field) {
                                                $viewName = str_replace('_', ' ', $field->name); // Replace underscores with spaces
                                                echo "<th>{$viewName}</th>";
                                            }
                                            ?>
                                            <th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Loop through all rows
                                        while ($row = $result->fetch_assoc()) {
                                            $id = $row['id'];
                                            echo "<tr>";
                                            foreach ($row as $value) {
                                                echo "<td style='border:1px solid grey!important;'>{$value}</td>";
                                            }
                                             echo "<td style='border:1px solid grey!important;'><a href = 'edit-user.php? id= $id'><i class='far fa-edit'></i></a></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scrollWrapper = document.querySelector('.table-scroll-wrapper');
        const tbody = scrollWrapper?.querySelector('tbody');
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

                //&& !e.target.parentElement.parentElement.classList.contains('ulList')
                if (scrollWrapper.scrollHeight > scrollWrapper.clientHeight && !(e.target.closest('.ulList'))) { // prevent from event trigger on dropdown scroll
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