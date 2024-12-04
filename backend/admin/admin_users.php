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
    <title>Manage Users</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Styling */
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #216491; color: white; }
        .form-group { margin-bottom: 20px; }
        .form-control { padding: 10px; width: 100%; border: 1px solid #ddd; border-radius: 5px; }
        .btn { padding: 10px 15px; background-color: #4c8cb4; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background-color: #216491; }
        .btn-danger { background-color: red; }
        .btn-danger:hover { background-color: darkred; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background-color: white; padding: 20px; border-radius: 5px; text-align: center; width: 300px; }
        .error { color: red; font-size: 14px; margin-top: 10px; }
    </style>



    <script>
        function showDeleteModal(username) {
            document.getElementById('deleteModal').style.display = 'flex';
            document.getElementById('usernameToDelete').value = username;
        }
        function hideDeleteModal() { document.getElementById('deleteModal').style.display = 'none'; }
        function showCredentialsModal(username) {
            document.getElementById('credentialsModal').style.display = 'flex';
            document.getElementById('usernameToView').value = username;
        }
        function hideCredentialsModal() { document.getElementById('credentialsModal').style.display = 'none'; }
        function updateStatus(username, newStatus) {
            const form = document.createElement('form');
            form.method = 'POST'; form.style.display = 'none';
            const usernameInput = document.createElement('input');
            usernameInput.type = 'hidden'; usernameInput.name = 'username'; usernameInput.value = username;
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden'; statusInput.name = 'new_status'; statusInput.value = newStatus;
            const updateStatusInput = document.createElement('input');
            updateStatusInput.type = 'hidden'; updateStatusInput.name = 'update_status';
            form.appendChild(usernameInput);
            form.appendChild(statusInput);
            form.appendChild(updateStatusInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>
<body>
    <h1>Manage Users</h1>
    <!-- Redirect to Add User Page -->
    <button class="btn btn-long" onclick="location.href='admin_add_user.php'">Add New User</button>


    <!-- Display Users -->
    <h2>All Users</h2>
    <table>
        <thead>
            <tr><th>Name</th><th>Surname</th><th>User ID</th><th>Email</th><th>Gender</th><th>Role</th><th>Created At</th><th>Status</th><th>Actions</th></tr>
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

