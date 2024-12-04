<?php
session_start();
include(__DIR__ . '/../../config/database.php');
include('../assets/inc/checklogin.php');
checklogin('admin'); // Only accessible by admin role

$mysqli = Database::getConnection();

// Fetch all users from the database using the `GetAllUsers` procedure
$users = [];
$stmt = $mysqli->prepare("CALL GetAllUsers()");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();
$mysqli->next_result(); // Free up the result set for subsequent queries

// Handle POST requests
$error = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        $adminPassword = sha1($_POST['admin_password']);
        $usernameToDelete = $_POST['username'];
        $adminUsername = $_SESSION['username'];

        // Verify admin password
        $stmt = $mysqli->prepare("SELECT UserID FROM USERS WHERE Username = ? AND PWD = ?");
        $stmt->bind_param('ss', $adminUsername, $adminPassword);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $deleteStmt = $mysqli->prepare("CALL DeleteUser(?)");
            $deleteStmt->bind_param('s', $usernameToDelete);
            $deleteStmt->execute();
            $deleteStmt->close();
            header("Location: admin_users.php"); // Refresh the page
            exit();
        } else {
            $error = "Wrong Password!";
        }
        $stmt->close();
    } elseif (isset($_POST['update_status'])) {
        $usernameToUpdate = $_POST['username'];
        $newStatus = $_POST['new_status'];
        $updateStmt = $mysqli->prepare("CALL UpdateUserStatus(?, ?)");
        $updateStmt->bind_param('ss', $usernameToUpdate, $newStatus);
        $updateStmt->execute();
        $updateStmt->close();
        header("Location: admin_users.php"); // Refresh the page
        exit();
    } elseif (isset($_POST['view_credentials'])) {
        $adminPassword = sha1($_POST['admin_password']);
        $usernameToView = $_POST['username'];
        $adminUsername = $_SESSION['username'];

        // Verify admin password
        $stmt = $mysqli->prepare("SELECT UserID FROM USERS WHERE Username = ? AND PWD = ?");
        $stmt->bind_param('ss', $adminUsername, $adminPassword);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $credentialsStmt = $mysqli->prepare("SELECT Username, PWD FROM USERS WHERE Username = ?");
            $credentialsStmt->bind_param('s', $usernameToView);
            $credentialsStmt->execute();
            $credentialsResult = $credentialsStmt->get_result()->fetch_assoc();
            $userCredentials = $credentialsResult;
            $credentialsStmt->close();
        } else {
            $error = "Wrong Password!";
        }
        $stmt->close();
    } elseif (isset($_POST['add_user'])) {
        $firstName = $_POST['first_name'];
        $lastName = $_POST['last_name'];
        $password = sha1($_POST['password']);
        $email = $_POST['email'];
        $role = $_POST['role'];
        $gender = $_POST['gender'];

        $addStmt = $mysqli->prepare("CALL AddUser(?, ?, ?, ?, ?, ?)");
        $addStmt->bind_param('ssssss', $firstName, $lastName, $password, $email, $role, $gender);
        $addStmt->execute();
        $addStmt->close();
        header("Location: admin_users.php"); // Refresh the page
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
        }
        

        .content-container {
            margin-top: 80px;
            /* Add space below the navbar */
            padding: 20px;
            margin-left: 200px;
            /* Ensure it doesn't overlap with the sidebar */
        }



        button {
            margin-bottom: 20px;
            /* Add spacing below the button */
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


        /* Table Styling */
        table {
            width: 100%;
            /* Make the table take the full width of the content container */
            border-collapse: collapse;
            margin: 20px 0;
            /* Add space between the table and the heading */
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px;
            /* Increase padding for better readability */
            text-align: center;
            /* Center-align the text */
            font-size: 16px;
            /* Make the text slightly larger */
        }

        table th {
            background-color: #1a4f6e;
            color: white;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
            /* Add a hover effect for better UX */
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn {
            padding: 10px 15px;
            background-color: #1a4f6e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Button Styling */
        .btn-long {
            display: block;
            width: 100%;
            /* Match the width of the table */
            text-align: center;
            /* Center-align the text */
            padding: 15px;
            /* Add padding for better spacing */
            background-color: #1a4f6e;
            color: white;
            font-size: 16px;
            /* Match the font size with the table */
            border: none;
            border-radius: 5px;
            margin: 20px 0;
            /* Add spacing above and below the button */
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-long:hover {
            background-color: #1a4f6e;
            /* Add hover effect */
        }


        .btn-danger {
            background-color: red;
        }

        .btn-danger:hover {
            background-color: darkred;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            width: 400px;
            /* Width of the modal */
        }

        .modal-content input[type="password"] {
            width: 80%;
            /* Make the input field smaller */
            margin: 10px auto;
            /* Add spacing around the input */
            padding: 8px;
            /* Adjust padding */
            font-size: 14px;
            /* Make text inside input smaller */
            display: block;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .modal-content button {
            margin-top: 15px;
            /* Add space between input field and buttons */
            margin-right: 10px;
            /* Add space between buttons */
            padding: 8px 12px;
            /* Adjust button size */
        }

        .modal-content button:last-of-type {
            margin-right: 0;
            /* Remove margin for the last button */
        }


        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
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



    <script>
        function showDeleteModal(username) {
            document.getElementById('deleteModal').style.display = 'flex';
            document.getElementById('usernameToDelete').value = username;
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        function showCredentialsModal(username) {
            document.getElementById('credentialsModal').style.display = 'flex';
            document.getElementById('usernameToView').value = username;
        }

        function hideCredentialsModal() {
            document.getElementById('credentialsModal').style.display = 'none';
        }

        function updateStatus(username, newStatus) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            const usernameInput = document.createElement('input');
            usernameInput.type = 'hidden';
            usernameInput.name = 'username';
            usernameInput.value = username;
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'new_status';
            statusInput.value = newStatus;
            const updateStatusInput = document.createElement('input');
            updateStatusInput.type = 'hidden';
            updateStatusInput.name = 'update_status';
            form.appendChild(usernameInput);
            form.appendChild(statusInput);
            form.appendChild(updateStatusInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
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
                <a href="admin_users.php" title="View Users" style="color: #ffc107;">> View Users</a>
                <a href="admin_add_user.php" title="Add New User">> Add New User</a>
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
    <div class="content-container">

        <h1>Users / View Users</h1>
        <!-- Redirect to Add User Page -->
        <button class="btn btn-long" onclick="location.href='admin_add_user.php'">Add New User</button>





        <!-- Display Users -->
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['First_Name']); ?></td>
                        <td><?php echo htmlspecialchars($user['Last_Name']); ?></td>
                        <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                        <td><?php echo htmlspecialchars($user['Email']); ?></td>
                        <td><?php echo htmlspecialchars($user['Gender']); ?></td>
                        <td><?php echo htmlspecialchars($user['User_Role']); ?></td>
                        <td><?php echo htmlspecialchars($user['Created_At']); ?></td>
                        <td><?php echo htmlspecialchars($user['User_Status']); ?></td>
                        <td>
                            <button onclick="updateStatus('<?php echo $user['Username']; ?>', '<?php echo $user['User_Status'] === 'Active' ? 'Inactive' : 'Active'; ?>')" class="btn">
                                <?php echo $user['User_Status'] === 'Active' ? 'Make Inactive' : 'Make Active'; ?>
                            </button>
                            <button onclick="showCredentialsModal('<?php echo $user['Username']; ?>')" class="btn"><i class="fas fa-eye"></i></button>
                            <button onclick="showDeleteModal('<?php echo $user['Username']; ?>')" class="btn btn-danger">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <h3>WARNING!! The user will be deleted forever.</h3>
            <p>Confirm Your Password:</p>
            <form method="POST">
                <input type="password" name="admin_password" class="form-control" required>
                <input type="hidden" name="username" id="usernameToDelete">
                <button type="submit" name="delete_user" class="btn btn-danger">Yes, Delete User</button>
                <button type="button" onclick="hideDeleteModal()" class="btn">No, Go Back</button>
            </form>
        </div>
    </div>

    <!-- View Credentials Modal -->
    <div class="modal" id="credentialsModal">
        <div class="modal-content">
            <h3>You are about to view secure credentials.</h3>
            <p>Confirm Your Password:</p>
            <form method="POST">
                <input type="password" name="admin_password" class="form-control" required>
                <input type="hidden" name="username" id="usernameToView">
                <button type="submit" name="view_credentials" class="btn">Confirm</button>
                <button type="button" onclick="hideCredentialsModal()" class="btn">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Credentials Display -->
    <?php if (isset($userCredentials)): ?>
        <div class="modal" id="viewCredentialsModal" style="display:flex;">
            <div class="modal-content">
                <h3>Credentials for <?php echo htmlspecialchars($userCredentials['Username']); ?></h3>
                <p>Password: <?php echo htmlspecialchars($userCredentials['PWD']); ?></p>
                <button type="button" onclick="hideViewCredentialsModal()" class="btn">Close</button>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Function to hide the "Confirm Password" modal
        function hideCredentialsModal() {
            document.getElementById('credentialsModal').style.display = 'none';
        }

        // Function to hide the "View Credentials" modal
        function hideViewCredentialsModal() {
            document.getElementById('viewCredentialsModal').style.display = 'none';
        }
    </script>