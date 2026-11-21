<?php
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
require_once __DIR__ . '/config/database.php';

// $selectRoles = "SELECT * FROM roles";
// $resultRoles = $conn->query($selectRoles);

$usersSql = "SELECT username, email FROM users";
$result = $conn->query($usersSql);

$usernames = [];
$emails = [];

while ($row = mysqli_fetch_assoc($result)) {
  $usernames[] = strtolower(trim($row['username']));
  $emails[] = strtolower(trim($row['email']));
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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $full_name = $_POST['full_name'];
  $email = $_POST['email'];
  $phone = str_replace('-', '', $_POST['phone']);
  $dob = $_POST['dob'];
  $username = $_POST['username'];
  $gender = $_POST['gender'];
  $password = $_POST['password'];
  $hiddenCount = $_POST['hiddenBrand'];
  // Hash the password
  $password_hashed = password_hash($password, PASSWORD_DEFAULT);
  $submittedUsername = strtolower(trim($username));
  $submittedEmail = strtolower(trim($email));

  //check existing username or email
  if (in_array($submittedUsername, $usernames)) {
    echo "<script>alert('Username already exists.'); window.history.back();</script>";
    exit;
  } elseif (in_array($submittedEmail, $emails)) {
    echo "<script>alert('Email already exists.'); window.history.back();</script>";
    exit;
  } else {
    // Insert query 
    $sql = "INSERT INTO users (full_name, email, phone_number, date_of_birth, username, gender, password) VALUES (
        '$full_name',
        '$email',
        '$phone',
        '$dob',
        '$username',
        '$gender',
        '$password_hashed'
    )";

    if ($conn->query($sql)) {
      $user_id = $conn->insert_id;
      for ($i = 1; $i <= $hiddenCount; $i++) {
        $brandKeys = (int)$_POST["brands-$i"];
        $rolesKeys = serialize($_POST["roles-$i"]);
        $entitiesKeys = serialize($_POST["entities-$i"]);

        $insertQuery = "INSERT INTO user_brand_role (user_id, brand_id, role_id, entity_id) 
                        VALUES ('$user_id', '$brandKeys', '$rolesKeys', '$entitiesKeys')";
        if (!$conn->query($insertQuery)) {
          echo "<script>alert('Failed to insert user-brand-role.');</script>";
        }
      }
      // -------after insert------
      echo "<script>
            alert('User added successfully.');
            window.location.href = 'add-user.php'; 
        </script>";
    } else {
      echo "<script>
            alert('Faileld to add user ');
            window.history.back(); 
        </script>";
    }
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
</style>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="content-wrapper">
      <div class="form-container">
        <h3>Add User</h3>
        <form method="POST" action="add-user.php" id="registrationForm">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="full_name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="phone" name="phone" placeholder="0xxxxxxxxx " pattern="(0[0-9]{2}|03[0-9]{2})[0-9]{7}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="dob" class="form-label">Date of Birth</label>
              <input type="date" class="form-control" id="dob" name="dob" min="1980-01-01" max="2090-12-31" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="gender" class="form-label">Gender</label>
              <select class="form-select" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="female">Female</option>
                <option value="male">Male</option>
              </select>
            </div>
          </div>
          <div class="row">

            <div class="col-md-12 mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
              <p style="cursor:pointer;" onclick="showpass()">show password</p>
            </div>
          </div>

          <div class="row icon-row" style="display:flex; justify-content:right;">
            <div class="col-12 text-end text-end-add" style="margin-bottom:10px; border-bottom:1px solid #eae3e3;">
              <i class="fa fa-plus add-button" aria-hidden="true" title="Add New Row" style="cursor:pointer;"></i>
            </div>
            <!-- --- -->
            <div class="row dropdown-row" data-id="1">
              <div class="col-md-4 mb-3">
                <label for="brands" class="form-label">brands</label>
                <select class="form-select brands" name="brands-1" required>
                  <?php echo '<option value="" disabled selected>Select brands</option>'; ?>
                  <?php
                  foreach ($brandData as $key => $value) {
                    echo "<option value='{$value['id']}'>{$value['brand']}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="roles" class="form-label">Roles</label>
                <select class="form-select roles" name="roles-1[]" required multiple="multiple" data-placeholder="Select option">
                  <?php
                  foreach ($roleData as $key => $value) {
                    echo "<option value='{$value['id']}' $selected>{$value['role']}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="entities" class="form-label">Entities</label>
                <select class="form-select entities" name="entities-1[]" required multiple="multiple" data-placeholder="Select option">
                  <?php
                  foreach ($entityData as $row => $value) {
                    echo "<option value='{$value['id']}' $selected>{$value['entity_name']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>
          <?php $rowCount = 1; ?>
          <input type="hidden" class="hiddenBrand" name="hiddenBrand" value="<?= $rowCount ?>">
          <div class="col-md-12 mb-3 pt-4">
            <div class="text-end">
              <button type="submit" class="btn btn-primary">SUBMIT</button>
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
                    ${brandsOptions}
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="roles" class="form-label">Roles</label>
                <select class="form-select roles" name="roles-${dataID}[]" required multiple="multiple" data-placeholder="Select option">
                    ${rolesOptions}
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="entities" class="form-label">Entities</label>
                <select class="form-select entities" name="entities-${dataID}[]" required multiple="multiple" data-placeholder="Select option">
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
<?php
include('includes/footer.php');
?>