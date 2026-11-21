<?php


require_once __DIR__ . '/config/database.php';
include __DIR__ . '/includes/lib/main_lib.php';
session_start();

// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
//     $input = trim($_POST['username']);
//     $password = $_POST['password'];

//     $query = "SELECT * FROM users WHERE (username = '$input' OR email = '$input') AND password = '$password'";
//     $result = $conn->query($query);
//     $user = $result->fetch_assoc();

//     if ($user) {
//         $_SESSION['auth']['user_id'] = $user['id'];
//         $_SESSION['auth']['username'] = $user['username'];
//         $_SESSION['auth']['last_active'] = time();
//         $_SESSION['auth']['logout_time'] = strtotime("+15 minutes",time());

//         header("Location: index.php");
//         exit;
//     } else {
//         echo "<script>alert('Invalid username/email or password.')</script>";
//         // echo "Invalid username/email or password.";
//     }

// }
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $input = trim($_POST['username']);
    $password = $_POST['password'];

    $input = mysqli_real_escape_string($conn, $input);

    $query = "SELECT * FROM users WHERE username = '$input' OR email = '$input'";
    $result = $conn->query($query);

    if ($result && $user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['auth']['user_id'] = $user['id'];
            $_SESSION['auth']['username'] = $user['username'];
            $_SESSION['auth']['last_active'] = time();
            $_SESSION['auth']['logout_time'] = strtotime("+25 minutes", time());
            //-------------------
            $is_admin = false;
            if (isset($user['role_id'])) {
                $roleId = (int)$user['role_id'];

                // Fetch the actual role name from the roles table
                $roleQuery = $conn->query("SELECT role FROM roles WHERE id = $roleId LIMIT 1");
                if ($roleQuery && $roleRow = $roleQuery->fetch_assoc()) {
                    $roleName = strtolower(trim($roleRow['role']));
                    if ($roleName === 'admin' || $roleName === 'administrator') {
                        $is_admin = true;
                    }
                }
            }
           

            //  If admin, grant full access
            if ($is_admin) {
                $_SESSION['is_admin'] = true;
                $_SESSION['access'] = 'all';
            } else {
                $_SESSION['is_admin'] = false;
                $ubr_query = "SELECT * FROM user_brand_role WHERE user_id = {$user['id']}";
                $ubr_result = $conn->query($ubr_query);

                $accessData = [];
                if ($ubr_result && $ubr_result->num_rows > 0) {
                    while ($ubr = $ubr_result->fetch_assoc()) {

                        $roleIds = unserialize($ubr['role_id']);
                        if ($roleIds === false && $ubr['role_id'] !== 'b:0;') {
                            $roleIds = explode(',', $ubr['role_id']);
                        }

                        $entityIds = unserialize($ubr['entity_id']);
                        if ($entityIds === false && $ubr['entity_id'] !== 'b:0;') {
                            $entityIds = explode(',', $ubr['entity_id']);
                        }


                        $brandName = '';
                        $brandRes = $conn->query("SELECT brand FROM brands WHERE id = {$ubr['brand_id']}");
                        if ($brandRes && $brandRes->num_rows > 0) {
                            $brandName = $brandRes->fetch_assoc()['brand'];
                        }

                        $roleNames = [];
                        if (!empty($roleIds)) {
                            $roleRes = $conn->query("SELECT role FROM roles WHERE id IN (" . implode(',', $roleIds) . ")");
                            while ($r = $roleRes->fetch_assoc()) {
                                $roleNames[] = $r['role'];
                            }
                        }

                        $entityNames = [];
                        if (!empty($entityIds)) {
                            $entityRes = $conn->query("SELECT entity_name FROM entities WHERE id IN (" . implode(',', $entityIds) . ")");
                            while ($e = $entityRes->fetch_assoc()) {
                                $entityNames[] = $e['entity_name'];
                            }
                        }

                        $accessData[] = [
                            'brand_id'      => $ubr['brand_id'],
                            'brand_name'    => $brandName,
                            'role_ids'      => $roleIds,
                            'role_names'    => $roleNames,
                            'entity_ids'    => $entityIds,
                            'entity_names'  => $entityNames
                        ];
                    }
                }

                //-------------------
                $_SESSION['access'] = $accessData;
            }

            header("Location: index.php");
            exit;
        }
    }
    echo "<script>alert('Invalid username/email or password.')</script>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/dist/css/style.css">
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="form-heading text-center mb-4">
            <h1>LOGIN</h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6 multi-step-main p-4 shadow rounded bg-white">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3 multi-step-input multi-step-form">
                        <label for="username" class="form-label">Username / Email</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>

                    <div class="mb-3 multi-step-input multi-step-form">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                        <div class="text-end mt-3">
                            <a href="forgot-password.php" class="text-decoration-none small text-primary">Forgot Password?</a>
                        </div>
                    </div>

                    <div class="button-group button-group-btn mt-4 login-btn text-center">
                        <button type="submit" name="login">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>