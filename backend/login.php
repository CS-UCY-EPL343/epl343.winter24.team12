<?php  
session_start();  
include('../config/database.php'); // Database connection

// Get the database connection
$mysqli = Database::getConnection();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']); // Hash the password using SHA1

    // Prepare and execute the query
    $stmt = $mysqli->prepare("SELECT UserID, User_Role FROM USERS WHERE Username = ? AND PWD = ?");
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $stmt->bind_result($user_id, $role);
    $rs = $stmt->fetch();

    if ($rs) {
        // Assign session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['User_Role'] = $role;

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
            default:  s
                // Redirect to a default page or show an error if the role is invalid
                $err = "Invalid Role. Please contact system administrator.";
        }
        exit;
    } else {
        // Invalid username or password
        $err = "Access Denied: Invalid Username or Password";
    }
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Custom Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #89f7fe, #66a6ff); /* Gradient background */
            min-height: 100vh;
            font-family: 'Poppins', sans-serif; /* Custom font */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); /* Card shadow */
            background-color: #ffffff;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #66a6ff, #89f7fe); /* Gradient button */
            border: none;
            font-size: 1.1rem;
            padding: 0.75rem;
            border-radius: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn-primary:hover {
            transform: scale(1.05); /* Button hover effect */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        h3 {
            font-weight: bold;
            color: #444444;
        }
        .alert {
            border-radius: 15px;
        }
        .form-control {
            border-radius: 10px;
        }
        .form-group label {
            font-weight: bold;
        }
        .container {
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center">Welcome to HIMAROS!</h3>
                        <?php if (isset($err)) { ?>
                            <div class="alert alert-danger text-center">
                                <?php echo htmlspecialchars($err); ?>
                            </div>
                        <?php } ?>
                        <form method="post">
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
