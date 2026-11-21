<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
include('./');

// Get lead ID from URL
$lead_id = isset($_GET['lead_id']) ? intval($_GET['lead_id']) : 0;
if (!$lead_id) {
    echo "<div class='alert alert-danger'>Invalid Lead ID</div>";
    exit;
}

// Get Business Managers
$managers = [];
$managerResult = $conn->query("SELECT id, full_name FROM users WHERE role_id = 11");
while ($row = $managerResult->fetch_assoc()) {
    $managers[] = $row;
}

// Get already assigned BMs (optional)
$assigned = [];
$assignedResult = $conn->query("SELECT business_manager_id FROM assigned_to_business_manager WHERE lead_id = $lead_id");
while ($row = $assignedResult->fetch_assoc()) {
    $assigned[] = $row['business_manager_id'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedBMs = $_POST['business_managers'] ?? [];

    foreach ($selectedBMs as $bmId) {
        $bmId = intval($bmId);

        // Check for duplicates
        $check = $conn->query("SELECT 1 FROM assigned_to_business_manager WHERE lead_id = $lead_id AND business_manager_id = $bmId");
        if ($check && $check->num_rows == 0) {
            $res = $conn->query("SELECT full_name FROM users WHERE id = $bmId");
            $bmName = $res->fetch_assoc()['full_name'];
            $bmNameEsc = $conn->real_escape_string($bmName);

            $conn->query("INSERT INTO assigned_to_business_manager (lead_id, business_manager_id, business_manager_name)
                          VALUES ($lead_id, $bmId, '$bmNameEsc')");
        }
    }

    echo "<script>alert('Business Manager(s) assigned successfully!'); window.location.href='leads.php';</script>";
    exit;
}
?>

<!-- Styles & Scripts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    .custom-multiselect { position: relative; width: 100%; }
    .select-box {
        padding: 10px; border: 1px solid #ccc;
        background: #fff; cursor: pointer; border-radius: 5px;
    }
    .options-container {
        display: none; position: absolute; top: 100%;
        left: 0; width: 100%; max-height: 200px;
        border: 1px solid #ccc; background: #fff;
        overflow-y: auto; z-index: 1000; box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .option { padding: 8px 10px; }
    .option:hover { background: #f1f1f1; }
</style>

<!-- Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST">
                <h4>Assign Business Managers to Lead (ID: <?= $lead_id ?>)</h4>

                <!-- Hidden select -->
                <select id="realSelect" name="business_managers[]" multiple style="display: none;">
                    <?php foreach ($managers as $bm): ?>
                        <option value="<?= $bm['id'] ?>" <?= in_array($bm['id'], $assigned) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($bm['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Custom dropdown -->
                <div class="custom-multiselect mt-3">
                    <div class="select-box">Select Business Managers</div>
                    <div class="options-container">
                        <input type="text" placeholder="Search..." class="form-control m-2" id="searchBox">
                        <div class="option">
                            <label><input type="checkbox" class="select-all"> Select All</label>
                        </div>
                        <?php foreach ($managers as $bm): ?>
                            <div class="option">
                                <label>
                                    <input type="checkbox"
                                           value="<?= $bm['id'] ?>"
                                           <?= in_array($bm['id'], $assigned) ? 'checked' : '' ?>
                                           data-name="<?= htmlspecialchars($bm['full_name']) ?>">
                                    <?= htmlspecialchars($bm['full_name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button class="btn btn-success mt-4">Assign</button>
                <a href="leads.php" class="btn btn-secondary mt-4 ml-2">Back</a>
            </form>
        </div>
    </div>
</div>

<!-- Script -->
<script>
$(document).ready(function () {
    const $dropdown = $(".options-container");
    const $box = $(".select-box");
    const $select = $("#realSelect");

    function updateBoxText() {
        const selected = $select.find("option:selected").map(function () {
            return $(this).text();
        }).get();
        $box.text(selected.length > 0 ? selected.join(", ") : "Select Business Managers");
    }

    $box.on("click", function (e) {
        e.stopPropagation();
        $(".options-container").not($dropdown).hide();
        $dropdown.toggle();
    });

    $dropdown.on("click", function (e) {
        e.stopPropagation();
    });

    $(document).on("click", function () {
        $dropdown.hide();
    });

    $dropdown.on("change", "input[type='checkbox']:not(.select-all)", function () {
        const val = $(this).val();
        const isChecked = $(this).is(":checked");
        $select.find(`option[value='${val}']`).prop("selected", isChecked);
        updateBoxText();
    });

    $(".select-all").on("change", function () {
        const checked = $(this).is(":checked");
        $dropdown.find("input[type='checkbox']:not(.select-all)").each(function () {
            $(this).prop("checked", checked).trigger("change");
        });
    });

    $("#searchBox").on("keyup", function () {
        const term = $(this).val().toLowerCase();
        $dropdown.find(".option").not(":first").each(function () {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(term));
        });
    });

    updateBoxText();
});
</script>

<?php include('includes/footer.php'); ?>
