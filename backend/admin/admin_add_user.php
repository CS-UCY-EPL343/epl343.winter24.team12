<?php
session_start();
include(__DIR__ . '/../../config/database.php');
include('../assets/inc/checklogin.php');
checklogin('admin'); // Only accessible by admin role

$mysqli = Database::getConnection();

// Handle POST request for adding users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $password = sha1($_POST['password']);
    $email = $_POST['email'];
    $role = $_POST['role'];
    $gender = $_POST['gender'];

    $stmt = $mysqli->prepare("CALL AddUser(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', $firstName, $lastName, $password, $email, $role, $gender);
    if ($stmt->execute()) {
        $success = "User added successfully!";
    } else {
        $error = "Failed to add user.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-control {
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            padding: 10px 15px;
            background-color: #4c8cb4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #216491;
        }
        .btn-clear {
            background-color: #ffcc00;
            margin-left: 10px;
        }
        .btn-clear:hover {
            background-color: #ff9900;
        }
        .error, .success {
            color: white;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .error {
            background-color: #e74c3c;
        }
        .success {
            background-color: #2ecc71;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add New User</h1>

        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="doctor">Doctor</option>
                    <option value="storekeeper">Storekeeper</option>
                    <option value="nurse">Nurse</option>
                    <option value="secretary">Secretary</option>
                </select>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <button type="submit" class="btn">Add</button>
            <button type="reset" class="btn btn-clear">Clear</button>
        </form>
    </div>
</body>
</html>
