<!-- <script src="/assets/dist/js/multiselect-dropdown.js"></script> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$access = $_SESSION['access'] ?? [];
$isAdmin = $_SESSION['is_admin'] ?? false;

$allowedBrands = [];
$allowedRoles = [];
$allowedEntities = [];

// Group permissions by brand
$brandWiseAccess = [];

if (!$isAdmin && !empty($access)) {
    foreach ($access as $item) {
        $brand = strtolower($item['brand_name']);

        $allowedBrands[] = $brand;

        $brandWiseAccess[$brand] = [
            'roles'    => array_map('strtolower', $item['role_names'] ?? []),
            'entities' => array_map('strtolower', $item['entity_names'] ?? [])
        ];

        $allowedRoles = array_merge($allowedRoles, array_map('strtolower', $item['role_names'] ?? []));
        $allowedEntities = array_merge($allowedEntities, array_map('strtolower', $item['entity_names'] ?? []));
    }

    $allowedBrands = array_unique($allowedBrands);
    $allowedRoles = array_unique($allowedRoles);
    $allowedEntities = array_unique($allowedEntities);
}
$allowedBrandsString = "'" . implode("','", $allowedBrands) . "'";
// echo '<pre>';
// print_r($allowedBrandsString);
// echo '</pre>';
// exit();

?>
<?php
include_once 'includes/header.php';
include 'includes/topbar.php';
include 'includes/sidebar.php';
//=========MainQuery=============
$where = "";
$select = array(
    "l.id as lead_id",
    "l.client_id as lead_client_id",
    "l.name as lead_client_name",
    "l.email as lead_client_email",
    "l.phone as lead_client_phone",
    "l.country as lead_client_country",
    "l.city as lead_client_city",
    "l.address as lead_client_address",
    "l.title as title",
    "l.short_description as short_description",
    "l.long_discription",
    "l.lead_creation_date as lead_creation_date",
    "l.last_updated as last_updated",
    "l.status as status",
    "l.specifics as specifics",
    "l.services as services",
    "l.budget as budget",
    "l.details as details",
    "l.lead_assigned_date",
    "c.id as client_id",
    "c.name as client_name",
    "c.email as client_email",
    "c.phone_number as client_phone",
    "c.country as client_country",
    "c.city as client_city",
    "c.address as client_address",
    "ad.developer_name as developer_assigned",
    // "ad.lead_id as lead_id",
    "abm.business_manager_name as business_manager_assigned",
    // "abm.lead_id as lead_id"
    "u.id as user_id",
    "u.full_name as user_name",
    "u.role_id as role_id"
);

$selectString = implode(",", $select);


$querySelect = "SELECT " . $selectString . "

FROM 
    leads AS l
LEFT JOIN assigned_to_developer  ad ON l.id = ad.lead_id
LEFT JOIN users  u ON ad.developer_name = u.full_name 
LEFT JOIN assigned_to_business_manager abm ON l.id = abm.lead_id
LEFT JOIN users bm ON abm.business_manager_name = bm.full_name 
LEFT JOIN clients c ON l.client_id = c.id";



// echo '<pre>';
// print_r($querySelect);
// echo '</pre>';
// exit();
//===============================
//----------leadtablequery-------
if ($isAdmin) {
    $sql = "SELECT * FROM leads";
} else {
    $sql = "SELECT * FROM leads WHERE brand IN($allowedBrandsString)";
}
//----------------get assigned devs-------------
$assignedDevs = [];
$devQuery = "SELECT lead_id, developer_id FROM assigned_to_developer";
$devResult = $conn->query($devQuery);
if ($devResult && $devResult->num_rows > 0) {
    while ($dev = $devResult->fetch_assoc()) {
        $leadId = $dev['lead_id'];
        $assignedDevs[$leadId][] = $dev['developer_id'];
    }
}
//--------- get assigned bus-managers
$assignedBmanagers = [];
$bmQuery = "SELECT lead_id, business_manager_id FROM assigned_to_business_manager";
$bmResult = $conn->query($bmQuery);
if ($bmResult && $bmResult->num_rows > 0) {
    while ($bm = $bmResult->fetch_assoc()) {
        $leadId = $bm['lead_id'];
        $assignedBmanagers[$leadId][] = $bm['business_manager_id'];
    }
}

// if ($isAdmin) {
//     $sql = "SELECT * FROM leads";
// } else {
//     $sql = "SELECT * FROM leads WHERE brand IN($allowedBrandsString)";
// }

$result = $conn->query($sql);
echo '<div class="container-fluid">';
echo '<div class="row">';
echo '<div class="col-sm-12">';
echo '<div class="content-wrapper">';
echo '<div class="table-wrapper">';
echo '<a href="link-setting.php?leads=leads&user=user&assigned_to_developer=assigned_to_developer&assigned_to_business_manager=assigned_to_business_manager" style="color:#6788c7;"><i class="fa-solid fa-gear link-setting"></i></a>';
echo '<div class="table-space ">';
echo '<div class="table-scroll-wrapper">';
echo '<form method="POST"  class="form-filter">';
if ($result->num_rows > 0) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    echo "<table style='border-collapse: collapse; width: 100%; border: 1px solid #ddd;' class='filter-table' id= 'leads-table'>";
    // Print table headers from first row
    echo '<thead><tr>';
    foreach (array_keys($rows[0]) as $column) {
        $column_display = str_replace('_', ' ', $column);
        if (in_array('none', $allowedBrands) && $column_display == 'brand') {
            continue;
        }
        echo "<th style='border: 1px solid #ddd; padding: 8px;'>$column_display</th>";
    }
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Developers Assigned</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Assign to Developer</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Business Manager Assigned </th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Assign to Business Manager</th>";

    // echo "<th style='border: 1px solid #ddd; padding: 8px;'>brand</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Estimation</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Contract</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Actions</th>";
    echo '</tr></tead><tbody>';
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($row as $key => $cell) {
            if (in_array('none', $allowedBrands) && $key == 'brand') {
                continue;
            }
            echo "<td style='border: 1px solid #ddd; padding: 8px;' data-field='$key'>$cell</td>";
        }

        // Add Action buttons
        $id = $row['id'];
        $brandName = $row['brand'];
        //--------------------------------------------
        $assignedList = isset($assignedDevs[$id]) ? implode(', ', $assignedDevs[$id]) : 'Not Assigned';
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>$assignedList</td>";

        //--------------------------developer--------------------
        $devSelect = "<select class='pulldown assign-developer' id='multiselect_" . $id . "' name='assignDeveloper' data-lead-id='$id' multiple multiselect-search='true' multiselect-select-all='true' multiselect-max-items='0' data-assigned='";
        $assignedDevsList = isset($assignedDevs[$id]) ? $assignedDevs[$id] : [];
        $assignedDevIds = [];
        $devOptionsHtml = '';
        // $developerResult = $conn->query("SELECT id, full_name FROM users ");
        $devQuery = "
    SELECT u.id, u.full_name, ubr.role_id
    FROM users u
    JOIN user_brand_role ubr ON ubr.user_id = u.id
    JOIN brands b ON b.id = ubr.brand_id
    WHERE b.brand = '$brandName'
";
        $developerResult = $conn->query($devQuery);
        // echo '<pre>';
        // print_r($developerResult);
        // echo '</pre>';
        // exit();
        while ($dev = $developerResult->fetch_assoc()) {
            $roles = @unserialize($dev['role_id']); // unserielize roles first
            if (!$roles || !is_array($roles)) continue;
            $roleIds = implode(',', array_map('intval', $roles));
            $roleQuery = "SELECT role FROM roles WHERE id IN ($roleIds)";
            $roleRes = $conn->query($roleQuery);
            $isDeveloper = false;
            while ($r = $roleRes->fetch_assoc()) {
                if (strtolower($r['role']) === 'developer') {
                    $isDeveloper = true;
                    break;
                }
            }
            if ($isDeveloper) {
                $devId = $dev['id'];
                $devName = htmlspecialchars($dev['full_name'], ENT_QUOTES, 'UTF-8');
                $selected = in_array($devId, $assignedDevsList) ? 'selected' : '';
                $devOptionsHtml .= "<option value='{$devId}' {$selected}>{$devName}</option>";
            }

       
        }
        $devSelect .= implode(',', $assignedDevIds) . "'>";
        $devSelect .= $devOptionsHtml;
        $devSelect .= "</select>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>$devSelect</td>";

        //-----------------------------------------------
        $bmAssignedList = isset($assignedBmanagers[$id]) ? implode(', ', $assignedBmanagers[$id]) : 'Not Assigned';
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>$bmAssignedList</td>";
        //-----------------------bus manager------------------------
        $bmSelect = "<select class='pulldown assign-bm' id='bm_multiselect_" . $id . "' name='assignBM' data-lead-id='$id' multiple multiselect-search='true' multiselect-select-all='true' multiselect-max-items='0' data-assigned='";

        $assignedBMList = isset($assignedBmanagers[$id]) ? $assignedBmanagers[$id] : [];
        $assignedBMIds = [];

        $bmOptionsHtml = '';
        $bmResult = $conn->query("SELECT id, full_name FROM users");
        while ($bm = $bmResult->fetch_assoc()) {
            $bmId = $bm['id'];
            $bmName = $bm['full_name'];

            $selected = in_array($bmName, $assignedBMList) ? 'selected' : '';
            if ($selected) $assignedBMIds[] = $bmId;

            $bmOptionsHtml .= "<option 
        value='{$bmId}' 
        id='bm_option_{$bmId}' 
        data-id='{$bmId}' 
        data-name='{$bmName}' 
        $selected>{$bmName}</option>";
        }
        $bmSelect .= implode(',', $assignedBMIds) . "'>";
        $bmSelect .= $bmOptionsHtml;
        $bmSelect .= "</select>";

        echo "<td style='border: 1px solid #ddd; padding: 8px;'>$bmSelect</td>";

        //-----------------------------------------------------    
        // echo "<td style='border: 1px solid #ddd; padding: 8px;'> ";
        // // echo "<a href='#?id=$id' class='btn btn-sm btn-primary'>b</a>";
        // echo " </td>";
        // echo "<td style='border: 1px solid #ddd; padding: 8px;'>
        //     <a href='#?id=$id' class='btn btn-sm btn-primary'>Creat Estimation</a>
        //     </td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>
            <button class='btn btn-sm btn-primary create-estimation' data-lead-id='$id'>Create Estimation</button>
            </td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>
            <a href='create-from-leads.php?id=$id' class='btn btn-sm btn-primary'>Creat contract</a>
            </td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>
            <a href='edit_lead.php?id=$id' class='btn btn-sm btn-primary'>Edit</a>
            <a href='edit_lead.php?id=$id&delete=1' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this lead?');\">Delete</a>
            </td>";
        echo '</tr>';
    }

    echo '</tbody></table>';
} else {
    echo '<p>No data found.</p>';
}

echo '</div>';
echo '</form>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
?>
<script>
    //------sync with multiselect-----------
    document.addEventListener('DOMContentLoaded', function() {
        // Loop through each custom dropdown wrapper
        document.querySelectorAll('.multiselect-dropdown').forEach(function(dropdown) {
            // Find related native <select> element
            const selectId = dropdown.id.replace('_ml', '');
            const select = document.getElementById(selectId);
            if (!select) return;

            // Map label text to option value
            const optionMap = {};
            select.querySelectorAll('option').forEach(opt => {
                optionMap[opt.textContent.trim()] = opt.value;
            });

            // Listen to checkbox changes
            dropdown.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                const label = checkbox.closest('div').querySelector('label');
                const labelText = label?.textContent.trim();
                const value = optionMap[labelText];

                checkbox.addEventListener('change', function() {
                    if (value) {
                        const opt = select.querySelector('option[value="' + value + '"]');
                        if (opt) opt.selected = this.checked;
                    }
                });

                // Sync initial state (load existing selections into checkboxes)
                if (value) {
                    const opt = select.querySelector('option[value="' + value + '"]');
                    if (opt && opt.selected) {
                        checkbox.checked = true;
                    }
                }
            });

            // If <select> changes, update checkboxes too
            select.addEventListener('change', function() {
                dropdown.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    const label = checkbox.closest('div').querySelector('label');
                    const labelText = label?.textContent.trim();
                    const value = optionMap[labelText];
                    if (value) {
                        const opt = select.querySelector('option[value="' + value + '"]');
                        checkbox.checked = opt && opt.selected;
                    }
                });
            });
        });
    });
    //--------------------------------

    //-----ajax----

    function submitDevelopers(select) {
        console.log('Submitting developers...');

        const leadId = select.getAttribute('data-lead-id');
        const selected = Array.from(select.selectedOptions);
        const submitted = new Set(); // Prevent duplicates

        selected.forEach(option => {
            const developerId = option.value;
            const developerName = option.dataset.name;
            const key = `${leadId}-${developerId}`;

            if (!submitted.has(key)) {
                submitted.add(key);

                console.log(`Posting Dev: Lead ID=${leadId}, Developer ID=${developerId}, Name=${developerName}`);

                $.post('assign_developers.php', {
                    lead_id: leadId,
                    developer_id: developerId,
                    developer_name: developerName
                }, function(response) {
                    console.log(`Response for Lead ${leadId} / Dev ${developerId}:`, response);
                });
            }
        });
    }


    function submitBusinessManagers(select) {
        console.log('Submitting business managers...');

        const leadId = select.getAttribute('data-lead-id');
        const selected = Array.from(select.selectedOptions);
        const submitted = new Set();

        selected.forEach(option => {
            const managerId = option.value;
            const managerName = option.dataset.name;
            const key = `${leadId}-${managerId}`;

            if (!submitted.has(key)) {
                submitted.add(key);

                console.log(`Posting BM: Lead ID=${leadId}, BM ID=${managerId}, Name=${managerName}`);

                $.post('assign_businessmanager.php', {
                    lead_id: leadId,
                    business_manager_id: managerId,
                    manager_name: managerName
                }, function(response) {
                    console.log(`Response for Lead ${leadId} / BM ${managerId}:`, response);
                });
            }
        });
    }

    //------------------unassign---------------
    document.addEventListener("DOMContentLoaded", function() {
        // Developer unassignment logic
        document.querySelectorAll('.assign-developer').forEach(function(select) {
            const previousAssigned = new Set(
                (select.getAttribute('data-assigned') || '').split(',').filter(Boolean)
            );

            select.addEventListener('change', function() {
                const leadId = this.getAttribute('data-lead-id');
                const currentSelected = new Set(
                    Array.from(this.selectedOptions).map(option => option.value)
                );

                // Unassign developers
                previousAssigned.forEach(devId => {
                    if (!currentSelected.has(devId)) {
                        $.post('unassign_developer.php', {
                            lead_id: leadId,
                            developer_id: devId
                        }, function(response) {
                            console.log(`Unassigned Dev ${devId} from Lead ${leadId}:`, response);
                        });
                    }
                });

                // Assign selected developers
                submitDevelopers(this);
                submitEstimation(leadId);
                // Update data-assigned state
                this.setAttribute('data-assigned', Array.from(currentSelected).join(','));
            });
        });

        // Business manager unassignment logic
        document.querySelectorAll('.assign-bm').forEach(function(select) {
            const previousAssigned = new Set(
                (select.getAttribute('data-assigned') || '').split(',').filter(Boolean)
            );

            select.addEventListener('change', function() {
                const leadId = this.getAttribute('data-lead-id');
                const currentSelected = new Set(
                    Array.from(this.selectedOptions).map(option => option.value)
                );

                previousAssigned.forEach(bmId => {
                    if (!currentSelected.has(bmId)) {
                        $.post('unassign_businessmanager.php', {
                            lead_id: leadId,
                            business_manager_id: bmId
                        }, function(response) {
                            console.log(`Unassigned BM ${bmId} from Lead ${leadId}:`, response);
                        });
                    }
                });

                submitBusinessManagers(this);
                submitEstimation(leadId);

                this.setAttribute('data-assigned', Array.from(currentSelected).join(','));
            });
        });
    });


    // -------------add estimation when dropdown selected-----------
    function submitEstimation(leadId) {
        const row = document.querySelector(`[data-lead-id='${leadId}']`).closest('tr');
        const rowData = {};

        // Extract all <td> with data-field attribute
        row.querySelectorAll('td[data-field]').forEach(td => {
            const field = td.getAttribute('data-field');
            const value = td.textContent.trim();
            rowData[field] = value;
        });

        // Get selected developers
        const devSelect = document.getElementById('multiselect_' + leadId);
        const selectedDevs = Array.from(devSelect.selectedOptions).map(opt => ({
            id: opt.value,
            name: opt.dataset.name
        }));

        // Get selected business managers
        const bmSelect = document.getElementById('bm_multiselect_' + leadId);
        const selectedBMs = Array.from(bmSelect.selectedOptions).map(opt => ({
            id: opt.value,
            name: opt.dataset.name
        }));

        // if (selectedDevs.length === 0 && selectedBMs.length === 0) {
        //     console.log('Estimation not triggered: both dropdowns are empty.');
        //     return;
        // }

        // Add devs and BMs to rowData
        rowData.developers = selectedDevs;
        rowData.business_managers = selectedBMs;

        $.ajax({
            url: 'addToEstimation.php',
            method: 'POST',
            data: {
                full_row_data: JSON.stringify(rowData)
            },
            success: function(response) {
                console.log('Estimation submitted with full row for Lead ID: ' + leadId);
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error('Error submitting estimation:', error);
            }
        });

    }
    //--------reload--
    //     document.addEventListener("DOMContentLoaded", function () {
    //     document.querySelectorAll('.assign-developer, .assign-bm').forEach(function(select) {
    //         select.addEventListener('change', function () {
    //             // Wait 1 second to let AJAX complete before reload
    //             setTimeout(function () {
    //                 location.reload();
    //             }, 2000);
    //         });
    //     });
    // });
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


<script src="/assets/dist/js/multiselect-dropdown.js"></script>
<?php //include('draw-table.php'); 
?>
<?php include('includes/footer.php'); ?>