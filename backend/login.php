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
    $stmt->bind_result($user_id, $user_role);
    $rs = $stmt->fetch();

    if ($rs) {
        // Assign session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user_role;

        // Redirect based on role
        switch ($user_role) {
            case 'admin':
                header("Location: admin/admin_dashboard.php");
                break;
            case 'doctor':
                header("Location: doctor/doc_dashboard.php");
                break;
            case 'nurse':
                header("Location: nurse/nurse_dashboard.php");
                break;
            case 'secretary':
                header("Location: secretary/secretary_dashboard.php");
                break;
            case 'storekeeper':
                header("Location: storekeeper/store_dashboard.php");
                break;
            default:
                $err = "Invalid Role. Please contact system administrator.";
        }
        exit;
    } else {
        // Invalid username or password
        $err = "Access Denied: Invalid Username or Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f8fc;
        }
        .left-section {
            width: 50%;
            background-color: #f4f8fc;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }
        .left-section img {
            max-width: 250px;
        }
        .right-section {
            width: 50%;
            background-color: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 40px 30px;
            width: 350px;
            text-align: center;
        }
        .card h3 {
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            text-align: left;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 20px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }
        .btn-primary {
            display: block;
            width: 100%;
            background: linear-gradient(135deg, #66a6ff, #89f7fe);
            border: none;
            padding: 12px;
            font-size: 16px;
            color: white;
            border-radius: 20px;
            cursor: pointer;
            margin-top: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="left-section">
        <img src="assets/images/logo.png" alt="OKYPY Logo">
    </div>
    <div class="right-section">
        <div class="card">
            <h3>Welcome to HIMAROS!</h3>
            <?php if (isset($err)) { ?>
                <div class="alert alert-danger text-center">
                    <?php echo htmlspecialchars($err); ?>
                </div>
            <?php } ?>
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn-primary">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
