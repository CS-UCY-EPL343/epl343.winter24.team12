<?php 
session_start();  
include('../config/database.php'); // Database connection

// Get the database connection
$mysqli = Database::getConnection();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']); // Hash the password using SHA1

    // Check if expected_role is set
    if (!isset($_POST['expected_role'])) {
        $err = "Role is not defined for this login.";
    } else {
        $expected_role = $_POST['expected_role']; // Get the expected role

        // Prepare and execute the query
        $stmt = $mysqli->prepare("SELECT UserID, Role FROM USERS WHERE Username = ? AND Password = ?");
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $stmt->bind_result($user_id, $role);
        $rs = $stmt->fetch();

        if ($rs) {
            if ($role == $expected_role) {
                // Assign session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                // Redirect based on role
                switch ($role) {
                    case 1: // Admin
                        header("Location: admin/admin_dashboard.php");
                        break;
                    case 2: // Doctor
                        header("Location: doctor/doc_dashboard.php");
                        break;
                    case 3: // Nurse
                        header("Location: nurse/nurse_dashboard.php");
                        break;
                    case 4: // Secretary
                        header("Location: secretary/secretary_dashboard.php");
                        break;
                }
                exit;
            } else {
                // Incorrect role
                $err = "Access Denied: Incorrect Role for this Login Page";
            }
        } else {
            // Invalid username or password
            $err = "Access Denied: Invalid Username or Password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="text-center">Login</h3>
                    <?php if (isset($err)) { ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
                    <?php } ?>
                    <form method="post">
                        <!-- Include hidden input for expected role -->
                        <input type="hidden" name="expected_role" value="<?php echo htmlspecialchars($_GET['role'] ?? ''); ?>">

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
