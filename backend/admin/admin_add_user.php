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
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
        }

        /* Top Navigation Bar */
        .navbar {
            background-color: #1a4f6e;
            color: white;
            display: flex;
            justify-content: space-between;
            padding: 10px 30px;
            align-items: center;
            height: 60px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
        }

        .navbar .icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .navbar .icons i {
            color: white;
            font-size: 20px;
            cursor: pointer;
        }

        /* Left-Side Navigation Bar */
        .sidebar {
            width: 180px;
            background-color: #1a4f6e;
            height: 100vh;
            position: fixed;
            top: 60px;
            left: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
            padding-left: 20px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            gap: 10px;
            padding: 10px 0;
            width: 100%;
        }

        .sidebar a:hover {
            background-color: #216491;
            border-radius: 10px;
        }

        .sidebar a.active {
            background-color: #ffc107;
            color: #1a4f6e;
            font-weight: bold;
            border-radius: 5px;
            margin-left: -10px;
            /* Added margin to shift it slightly left */
            padding: 10px 1px;
            flex-direction: column;
        }

        .sidebar i {
            font-size: 18px;
        }

        /* Align Add New User form styling with Add New Operation */
        .dashboard-container {
            margin-top: 80px;
            margin-left: 200px;
            padding: 20px;
            width: calc(100% - 200px);
        }

        .form-container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: left;
        }

        .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            flex: 1;
            font-weight: bold;
            margin-right: 10px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            flex: 2;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group input::placeholder {
            color: #999;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 15px;
        }

        .button-container button {
            padding: 15px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .button-container .clear-btn {
            background-color: #f8d7da;
            color: #721c24;
        }

        .button-container .cancel-btn {
            background-color: #f4f4f4;
            color: #333;
            border: 1px solid #ccc;
        }

        .button-container .add-btn {
            background-color: #1a4f6e;
            color: white;
        }


        .error,
        .success {
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

        .users-dropdown {
            width: 90%;
        }

        .users-dropdown a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffc107;
            color: #1a4f6e;
            font-size: 14px;
            font-weight: bold;
            padding: 5px 7px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .users-dropdown .dropdown-arrow {
            font-size: 12px;
            transition: transform 0.3s;
        }

        .users-dropdown .dropdown-content {
            display: none;
            flex-direction: column;
            padding-left: 15px;
            margin-top: 5px;
        }

        .users-dropdown .dropdown-content a {
            font-size: 14px;
            color: white;
            text-decoration: none;
            padding: 5px 10PX;
            transition: color 0.2s;
            background-color: transparent;
        }

        .users-dropdown .dropdown-content a:hover {
            color: #ffc107;
        }

        .users-dropdown.open .dropdown-content {
            display: flex;
        }

        .users-dropdown.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        .users-dropdown .dropdown-content a.active {
            color: #ffc107;
            /* Yellow color for active */
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Top Navigation Bar -->
    <div class="navbar">
        <div class="logo">
            <strong>HIMAROS</strong>
        </div>
        <div class="icons">
            <i class="fas fa-folder" title="Files"></i>
            <i class="fas fa-cog" title="Settings"></i>
            <i class="fas fa-user-circle" title="Profile"></i>
            <!-- Add Logout Icon -->
            <a href="../common/logout.php" title="Logout" style="color: white; text-decoration: none; margin-left: 15px;">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <!-- Left Side Navigation Bar -->
    <div class="sidebar">
        <a href="admin_dashboard.php" title="Dashboard">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="admin_inventory.php" title="Inventory">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <a href="admin_operations.php" title="Operations">
            <i class="fas fa-stethoscope"></i> Operations
        </a>
        <a href="admin_supplier.php" title="Suppliers">
            <i class="fas fa-truck"></i> Suppliers
        </a>
        <a href="#" title="Reports">
            <i class="fas fa-chart-line"></i> Reports
        </a>
        <div class="users-dropdown open">
            <a href="admin_users.php" class="active" title="Users" onclick="toggleDropdown(event)" style="background-color: #ffc107; color: #1a4f6e; font-size: 18px; font-weight: bold; padding: 10px; border-radius: 5px;">
                <i class="fas fa-users"></i> Users <i class="fas fa-chevron-down dropdown-arrow"></i>
            </a>
            <div class="dropdown-content">
                <a href="admin_users.php" title="View Users">> View Users</a>
                <a href="admin_add_user.php" title="Add New User" style="color: #ffc107;">> Add New User</a>
            </div>
        </div>

    </div>

    <script>
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.target.closest('.operations-dropdown');
            dropdown.classList.toggle('open');
        }
    </script>

    <div class="dashboard-container">
        <h1>Add New User</h1>
        <div class="form-container">
            <h2>Enter User Details</h2>

            <?php if (isset($success)): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter first name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter last name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter email address" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="doctor">Doctor</option>
                        <option value="storekeeper">Storekeeper</option>
                        <option value="nurse">Nurse</option>
                        <option value="secretary">Secretary</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="button-container">
                    <button type="reset" class="clear-btn">Clear Form</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='admin_users.php';">Cancel</button>
                    <button type="submit" class="add-btn">Add User</button>
                </div>

            </form>
        </div>
</body>

</html>