    <?php
    include_once 'includes/header.php';
    include 'includes/topbar.php';
    include 'includes/sidebar.php';
    //---------


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST) {

        $allRoles = $_POST['roles'] ?? [];
        $submittedPermissions = $_POST['permissions'] ?? [];

        foreach ($allRoles as $role_id) {
            $role_id = (int)$role_id;
            $permission_ids = $submittedPermissions[$role_id] ?? [];

            //  Remove unchecked permissions for this role
            if (!empty($permission_ids)) {
                // Convert array of IDs to comma-separated string
                $ids = implode(',', array_map('intval', $permission_ids));
                $deleteSQL = "DELETE FROM role_permissions WHERE role_id = $role_id AND permission_id NOT IN ($ids)";
            } else {
                // No permissions submitted — delete all for this role
                $deleteSQL = "DELETE FROM role_permissions WHERE role_id = $role_id";
            }

            $conn->query($deleteSQL);

            //  Insert new checked permissions
            foreach ($permission_ids as $permission_id) {
                $permission_id = (int)$permission_id;

                $insertSQL = "
                    INSERT INTO role_permissions (role_id, permission_id)
                    SELECT * FROM (SELECT $role_id AS role_id, $permission_id AS permission_id) AS tmp
                    WHERE NOT EXISTS (
                        SELECT 1 FROM role_permissions WHERE role_id = $role_id AND permission_id = $permission_id
                    ) LIMIT 1
                ";

                $conn->query($insertSQL);
            }
        }

        //  Re-fetch updated permissions for checkbox display
        $rolePermissions = [];
        $resultRolePermissions = $conn->query("SELECT role_id, permission_id FROM role_permissions");
        while ($row = $resultRolePermissions->fetch_assoc()) {
            $rolePermissions[$row['role_id']][$row['permission_id']] = true;
        }
    }
    //---------
    // Fetch current role-permission mappings
    $rolePermissions = [];
    $resultRolePermissions = $conn->query("SELECT role_id, permission_id FROM role_permissions");
    while ($row = $resultRolePermissions->fetch_assoc()) {
        $rolePermissions[$row['role_id']][$row['permission_id']] = true;
    }

    // Fetch all roles
    $roles = [];
    $resultRoles = $conn->query("SELECT id, role FROM roles");
    while ($row = $resultRoles->fetch_assoc()) {
        $roles[] = $row;
    }
    // Fetch all permissions
    $permissions = [];
    $resultPermissions = $conn->query("SELECT id, permission_slug FROM permissions");
    while ($row = $resultPermissions->fetch_assoc()) {
        $permissions[] = $row;
    }


    echo '<div class="container-fluid">';
    echo '<div class="row">';
    echo '<div class="col-sm-12">';
    echo '<div class="content-wrapper" style="overflow:scroll;">';
    echo '<div class="table-wrapper"  style="display:flex;">';
    echo '<div class="table-scroll-wrapper">';
    echo '<form id="data-form" name="data-form" method="POST" class="form-filter">';
    echo '<table style="border-collapse: collapse;" class="filter-table">';
    echo '<thead><tr><th>Role</th>';
    foreach ($permissions as $permit) {
        echo '<th>' . htmlspecialchars($permit['permission_slug']) . '</th>';
    }
    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($roles as $role) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($role['role']) . '</td>';
        // Hidden field to ensure role is submitted even if all boxes are unchecked
        echo '<input type="hidden" name="roles[]" value="' . $role['id'] . '">';

        foreach ($permissions as $permit) {
            $checked = isset($rolePermissions[$role['id']][$permit['id']]) ? 'checked' : '';
            echo '<td><input type="checkbox" name="permissions[' . $role['id'] . '][]" value="' . $permit['id'] . '" data-id= "' . $permit['id'] . '" ' . $checked . '></td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '<br><input type="submit" value="Save Permissions">';
    echo '</form>';
    echo '</div>';
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
