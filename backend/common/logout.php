<?php
session_start();

// Check if the user confirmed the logout
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Destroy the session and redirect to login
    session_destroy();
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            text-align: center;
            margin-top: 100px;
        }
        .confirm-box {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }
        .confirm-btn {
            background-color: #ff5e57;
            color: white;
        }
        .cancel-btn {
            background-color: #54bae9;
            color: white;
        }
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="confirm-box">
        <h1>Are you sure you want to log out?</h1>
        <!-- Confirmation and Cancel Buttons -->
        <button class="confirm-btn" onclick="confirmLogout()">Yes</button>
        <button class="cancel-btn" onclick="cancelLogout()">No</button>
    </div>

    <script>
        function confirmLogout() {
            // Redirect with confirmation
            window.location.href = 'logout.php?confirm=yes';
        }

        function cancelLogout() {
            // Go back to the previous page
            window.history.back();
        }
    </script>
</body>
</html>
