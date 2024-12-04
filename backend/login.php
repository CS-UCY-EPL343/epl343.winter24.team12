<?php
session_start();
include('../config/database.php'); // Database connection

// Get the database connection
$mysqli = Database::getConnection();

$err = ''; // Initialize error message variable

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']); // Hash the password using SHA1

    // Prepare and execute the query
    $stmt = $mysqli->prepare("SELECT UserID, User_Role FROM USERS WHERE Username = ? AND PWD = ?");
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $stmt->bind_result($user_id, $user_role);

    if ($stmt->fetch()) {
        // Assign session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['User_Role'] = $user_role;

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
                $err = "Invalid Role. Please contact the system administrator.";
                break;
        }
        exit;
    } else {
        // Invalid username or password
        $err = "Access Denied: Invalid Username or Password";
    }

    $stmt->close();
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
            background: linear-gradient(to right, #fff 50%, #216491 50%);
        }
        .left-section {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0;
            overflow: hidden;
        }
        .left-section img {
            max-width: 80%;
            max-height: 80%;
            object-fit: contain;
        }
        .right-section {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
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
            border-color: #216491;
            box-shadow: 0 0 5px rgba(33, 100, 145, 0.5);
            outline: none;
        }
        .btn-primary {
            display: block;
            width: 100%;
            background-color: #4c8cb4;
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
        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="left-section">
        <img src="../assets/images/logo.jpg" alt="HIMAROS Logo">
    </div>
    <div class="right-section">
        <div class="card">
            <h3>Welcome to HIMAROS!</h3>
            <?php if (!empty($err)) { ?>
                <div class="alert"><?php echo htmlspecialchars($err); ?></div>
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
