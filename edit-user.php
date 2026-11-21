<?php
include_once('includes/header.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');
include_once('select2links.php');
$id = (int)$_GET['id'];
$sql = "SELECT * FROM users WHERE id = $id ";
$result = $conn->query($sql);
$rowdata = '';
while ($row = mysqli_fetch_assoc($result)) {
    $rowdata = $row;
}

//----roleQuery------
$roleData = [];
$sqlrole = "SELECT id, role FROM roles ";
$resultrole = $conn->query($sqlrole);
while ($row  = mysqli_fetch_assoc($resultrole)) {
    $roleData[] = $row;
}

//-----brandsQuery---
$sqlBrand = "SELECT id, brand FROM brands ";
$resultBrand = $conn->query($sqlBrand);
$brandData = [];
while ($row = mysqli_fetch_assoc($resultBrand)) {
    $brandData[] = $row;
}

//--------
//-----entitiesQuery---
$sqlEntity = "SELECT id, entity_name FROM entities ";
$resultEntity = $conn->query($sqlEntity);
$entityData = [];
while ($row = mysqli_fetch_assoc($resultEntity)) {
    $entityData[] = $row;
}
//----userBrandRoleQuery-
$userBrandRoles = [];
$ubrSelect = "SELECT * FROM user_brand_role where user_id = $id";
$resultUbr = $conn->query($ubrSelect);
while ($row = mysqli_fetch_assoc($resultUbr)) {
    $userBrandRoles[] = [
        'brand_id' => $row['brand_id'],
        'role_ids' => ($row['role_id']) ? @unserialize($row['role_id']) : [],
        'entity_ids' => ($row['entity_id']) ? @unserialize($row['entity_id']) : [],
    ];
}


//--------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        die('User ID is missing.');
    }

    // Sanitize inputs
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $password = trim($_POST['password']);
    $hiddenCount = $_POST['hiddenBrand'];
    //------deleteQuery-----
    $postBrandIds = [];
    for ($i = 1; $i <= $hiddenCount; $i++) {
        if (!empty($_POST["brands-$i"])) {
            $postBrandIds[] = (int)$_POST["brands-$i"];
        }
    }

    // delete brand id which is not in post data
    if (!empty($postBrandIds)) {
        $brandIdsStr = implode(',', $postBrandIds);
        $deleteQuery = "DELETE FROM user_brand_role 
                    WHERE user_id = $id 
                    AND brand_id NOT IN ($brandIdsStr)";
    } else {

        $deleteQuery = "DELETE FROM user_brand_role WHERE user_id = $id";
    }
    $conn->query($deleteQuery);

    //------enddelete-------

    for ($i = 1; $i <= $hiddenCount; $i++) {

        $brandKeys = (int)$_POST["brands-$i"];
        // $rolesKeys = serialize($_POST["roles-$i"]);
        $rolesKeys = isset($_POST["roles-$i"]) ? serialize($_POST["roles-$i"]) : null;
        // $entitiesKeys = serialize($_POST["entities-$i"]);
        $entitiesKeys = isset($_POST["entities-$i"]) ? serialize($_POST["entities-$i"]) : null;

        $checkQuery = "SELECT id FROM user_brand_role WHERE user_id = '$id' AND brand_id = '$brandKeys' LIMIT 1";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {

            $updateQuery = "UPDATE user_brand_role 
                        SET role_id = '$rolesKeys', entity_id = '$entitiesKeys' 
                        WHERE user_id = '$id' AND brand_id = '$brandKeys'";
            if (!$conn->query($updateQuery)) {
                echo "<script>alert('Failed to update user-brand-role.');</script>";
            }
        } else {

            $insertQuery = "INSERT INTO user_brand_role (user_id, brand_id, role_id, entity_id) 
                        VALUES ('$id', '$brandKeys', '$rolesKeys', '$entitiesKeys')";
            if (!$conn->query($insertQuery)) {
                echo "<script>alert('Failed to insert user-brand-role.');</script>";
            }
        }
    }

    // If password field is not empty, update it
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET 
                    full_name = '$full_name',
                    email = '$email',
                    phone_number = '$phone',
                    date_of_birth = '$dob',
                    username = '$username',
                    gender = '$gender',
                    
                    password = '$hashed_password'
                WHERE id = $id";
    } else {
        // Don't update password if it's left blank
        $sql = "UPDATE users SET 
                    full_name = '$full_name',
                    email = '$email',
                    phone_number = '$phone',
                    date_of_birth = '$dob',
                    username = '$username',
                    gender = '$gender'
                WHERE id = $id";
    }
    if ($conn->query($sql)) {
        echo "<script>alert('User updated successfully.'); window.location.href='show-users.php';</script>";
    } else {
        echo "<script>alert('Failed to update user.'); window.history.back();</script>";
    }
}

?>
<style>
    body {
        background-color: #f9f9f9;
    }

    .form-container {
        background-color: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h3 {
        font-weight: 600;
        margin-bottom: 25px;
    }

    .btn-primary {
        padding: 10px 30px;
        font-weight: 500;
    }

    .select2-selection__choice__display {
        background-color: rgba(186, 209, 251, 0.5);
    }
</style>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="content-wrapper">
                <div class="form-container">
                    <h3>Edit User ID: <?= $id ?></h3>
                    <form method="POST" action="edit-user.php?id=<?= $id ?>" id="editForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= $rowdata['full_name'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= $rowdata['email'] ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?= $rowdata['phone_number'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" value="<?= $rowdata['date_of_birth'] ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= $rowdata['username'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="female" <?= $rowdata['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                                    <option value="male" <?= $rowdata['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <p style="cursor:pointer;" onclick="showpass()">Show Password</p>
                            </div>
                        </div>
                        <div class="row icon-row" style="display:flex; justify-content:right;">
                            <div class="col-12 text-end text-end-add" style="margin-bottom:10px; border-bottom:1px solid #eae3e3;">
                                <i class="fa fa-plus add-button" aria-hidden="true" title="Add New Row" style="cursor:pointer;"></i>
                            </div>
                            <!-- ---------------- -->
                            <?php if (empty($userBrandRoles)) { ?>
                                <div class="row dropdown-row" data-id="1">
                                    <div class="col-md-4 mb-3">
                                        <label for="brands" class="form-label">brands</label>
                                        <select class="form-select brands" name="brands-1">
                                            <?php echo '<option value="" disabled selected>Select brands</option>'; ?>
                                            <?php echo '<option value="4">None </option>'; ?>
                                            <?php

                                            foreach ($brandData as $key => $value) {
                                                echo "<option value='{$value['id']}' $selected>{$value['brand']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="roles" class="form-label">Roles</label>
                                        <select class="form-select roles" name="roles-1[]"  multiple="multiple" data-placeholder="Select option">
                                            <?php //echo '<option value="" disabled selected>Select Role</option>'; 
                                            ?>
                                            <?php
                                            foreach ($roleData as $key => $value) {
                                                $selected = '';
                                                if (is_array($val['role_ids'])) {
                                                    $selected = in_array($value['id'], $val['role_ids']) ? 'selected' : '';
                                                }
                                                // $selected = ($rowdata['role_id'] == $value['id']) ? 'selected' : '';
                                                echo "<option value='{$value['id']}' $selected>{$value['role']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="entities" class="form-label">Entities</label>
                                        <select class="form-select entities" name="entities-1[]"  multiple="multiple" data-placeholder="Select option">
                                            <?php //echo '<option value="" disabled selected>Select Entity</option>'; 
                                            ?>
                                            <?php
                                            foreach ($entityData as $row => $value) {
                                                $selected = '';
                                                if (is_array($val['entity_ids'])) {
                                                    $selected = in_array($value['id'], $val['entity_ids']) ? 'selected' : '';
                                                }
                                                echo "<option value='{$value['id']}' $selected>{$value['entity_name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <!-- Copy code -->
                                <?php
                                // echo '<pre>';
                                // print_r($userBrandRoles);
                                // echo '</pre>';
                                // exit();

                                foreach ($userBrandRoles as $keys => $val) : ?>
                                    <?php $index = $keys + 1; ?>
                                    <div class="row dropdown-row" data-id="<?= $index ?>">
                                        <div class="col-12 text-end" style="margin-bottom:10px;">
                                            <i class="fa-solid fa-x remove-button" title="Remove row" style="cursor:pointer;"></i>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="brands" class="form-label">brands</label>
                                            <select class="form-select brands" name="brands-<?= $index ?>" >
                                                <?php echo '<option value="" disabled selected>Select brands</option>'; ?>
                                                <?php echo '<option value="4">None </option>'; ?>
                                                <?php
                                                foreach ($brandData as $key => $value) {
                                                    $selected = ($value['id'] == $val['brand_id']) ? 'selected' : '';
                                                    echo "<option value='{$value['id']}' $selected>{$value['brand']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="roles" class="form-label">Roles</label>
                                            <select class="form-select roles" name="roles-<?= $index ?>[]"  multiple="multiple" data-placeholder="Select option">
                                                <?php //echo '<option value="" disabled selected>Select Role</option>'; 
                                                ?>
                                                <?php
                                                foreach ($roleData as $key => $value) {
                                                    $selected = '';
                                                    if (is_array($val['role_ids'])) {
                                                        $selected = in_array($value['id'], $val['role_ids']) ? 'selected' : '';
                                                    }
                                                    echo "<option value='{$value['id']}' $selected>{$value['role']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="entities" class="form-label">Entities</label>
                                            <select class="form-select entities" name="entities-<?= $index ?>[]" multiple="multiple" data-placeholder="Select option">
                                                <?php //echo '<option value="" disabled selected>Select Entity</option>'; 
                                                ?>
                                                <?php
                                                foreach ($entityData as $row => $value) {
                                                    $selected = '';
                                                    if (is_array($val['role_ids'])) {
                                                        $selected = in_array($value['id'], $val['entity_ids']) ? 'selected' : '';
                                                    }
                                                    // $selected = in_array($value['id'], $val['entity_ids']) ? 'selected' : '';
                                                    echo "<option value='{$value['id']}' $selected>{$value['entity_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            <?php } //end if else
                            ?>
                            <!-- end -->

                            </div>
                            <?php $rowCount = count($userBrandRoles) > 0 ? count($userBrandRoles) : 1; ?>
                            <input class="hiddenBrand" type="hidden" name="hiddenBrand" data-id="1" value="<?= $rowCount ?>">

                            <div class="col-md-12 mb-3 pt-4">
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">UPDATE</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.entities').select2();
        $('.roles').select2({
            placeholder: "Select Role",
            allowClear: true
        });
    });

    function showpass() {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
    const brandsObj = <?= json_encode($brandData); ?>;
    const rolesObj = <?= json_encode($roleData); ?>;
    const entitiesObj = <?= json_encode($entityData); ?>;

    var brandsOptions = '<option value="" disabled selected>Select brands</option>';
    var rolesOptions = '';
    var entitiesOptions = '';
    brandsObj.forEach(function(brand) {
        brandsOptions += '<option value="' + brand.id + '" data-val="' + brand.brand + '">' + brand.brand + '</option>'
    });
    rolesObj.forEach(function(role) {
        rolesOptions += '<option value="' + role.id + '" data-val="' + role.role + '">' + role.role + '</option>'
    });
    entitiesObj.forEach(function(entity_name) {
        entitiesOptions += '<option value="' + entity_name.id + '" data-val="' + entity_name.entity_name + '">' + entity_name.entity_name + '</option>'
    });




    //newrow
    $('.add-button').click(function() {

        var dataID = $('.dropdown-row').length + 1;

        var dropdownRow = `
        <div class="row dropdown-row" data-id="${dataID}">
             <div class="col-12 text-end">
                <i class="fa-solid fa-x remove-button" title="Remove row" style="cursor:pointer;"></i>
            </div>
            <div class="col-md-4 mb-3">
                <label for="brands" class="form-label">brands</label>
                <select class="form-select brands"  name="brands-${dataID}" required>
                '<option value="4">None </option>'
                    ${brandsOptions}
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="roles" class="form-label">Roles</label>
                <select class="form-select roles" name="roles-${dataID}[]"  multiple="multiple" data-placeholder="Select option">
                    ${rolesOptions}
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="entities" class="form-label">Entities</label>
                <select class="form-select entities" name="entities-${dataID}[]"  multiple="multiple" data-placeholder="Select option">
                    ${entitiesOptions}
                </select>
            </div>

        </div>`;

        var newRow = $(dropdownRow);
        if ($('.dropdown-row').length > 0) {
            $('.dropdown-row:last').after(newRow);
        } else {
            $('.text-end-add').after(newRow);
        }

        newRow.find('.roles').select2({
            placeholder: "Select Role"
        });
        newRow.find('.entities').select2({
            placeholder: "Select Entities"
        });

        var x = parseInt($('.hiddenBrand').val());
        x++;
        $('.hiddenBrand').val(x);
    });
    $(document).on('click', '.remove-button', function() {
        $(this).closest('.dropdown-row').remove();
        var dropdown_row_id = $('.dropdown-row');
        console.log(dropdown_row_id);
        dropdown_row_id.each(function(index) {
            var newIndex = index + 1;
            $(this).attr('data-id', newIndex);
            $(this).find('.brands').attr('name', 'brands-' + newIndex);
            $(this).find('.roles').attr('name', 'roles-' + newIndex + '[]');
            $(this).find('.entities').attr('name', 'entities-' + newIndex + '[]');
        });
        $('.hiddenBrand').val($('.dropdown-row').length);

    });
</script>
<?php include('includes/footer.php'); ?>