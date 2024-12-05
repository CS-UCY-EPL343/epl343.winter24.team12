<?php
session_start();  // Ensure session is started

// Check if the user is logged in and their role is set
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the user's role
$role = $_SESSION['role'];

// Define dashboard links for each role
$dashboard_links = [
    'admin' => '../admin/admin_inventory.php',
    'doctor' => '../doctor/doc_inventory.php',
    'nurse' => '../nurse/nurse_inventory.php',
    'secretary' => '../secretary/secretary_inventory.php',
];

// Ensure that we have a valid role and redirect to the appropriate dashboard
if (isset($dashboard_links[$role])) {
    $back_link = $dashboard_links[$role];
} else {
    $back_link = '../login.php';  // Default to login page if the role is unknown
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
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

        .dashboard-container {
            margin: 100px auto;
            max-width: 800px;
            padding: 20px;
        }

        h1 {
            color: black;
            margin-bottom: 30px;
            font-size: 28px;
            text-align: left;
            font-weight: bold;
        }

        .add-item-container {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .add-item-button {
            display: block;
            width: 100%;
            margin: 20px 0;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            background-color: #1a4f6e;
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .add-item-button i {
            margin-left: 10px;
            font-size: 20px;
        }

        .add-item-button:hover {
            background-color: #155bb5;
            transform: scale(1.05);
        }

        /* Back Button */
        .back-button {
            background-color: #1a4f6e;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #155bb5;
        }

        .back-button i {
            font-size: 16px;
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

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Page Title -->
        <h1>Add Item</h1>

        <!-- Add Item Container -->
        <div class="add-item-container">
            <button class="add-item-button" onclick="location.href='new_item.php'">
                Add New Item <i class="fas fa-box-open"></i>
            </button>
            <button class="add-item-button" onclick="location.href='restock_item.php'">
                Restock Existing Item <i class="fas fa-box"></i>
            </button>
        </div>

        <!-- Back Button -->
        <button class="back-button" onclick="history.back();">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>
</body>

</html>