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
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        /* Top Bar */
        .top-bar {
            background-color: #216491;
            color: white;
            padding: 15px;
            text-align: center;
            position: relative;
        }

        .top-bar h1 {
            margin: 0;
            font-size: 24px;
        }

        /* Back Button */
        .back-button {
            position: absolute;
            top: 10px; /* Adjusted for better alignment */
            left: 10px;
            background-color: #1a4f6e;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px; /* Reduced padding for compact look */
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            background-color: #155bb5;
        }

        /* Add Item Container */
        .add-item-container {
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            text-align: center;
        }

        .add-item-container h2 {
            margin: 0 0 20px;
            font-size: 22px;
            color: #333;
        }

        .add-item-button {
            display: block;
            margin: 10px auto;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            background-color: #f0f0f0;
            border: 2px solid #ddd;
            border-radius: 5px;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            width: 90%;
        }

        .add-item-button:hover {
            background-color: #216491;
            color: white;
        }

        .add-item-button i {
            margin-left: 10px;
        }
    </style>
</head>

<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <a href="<?php echo htmlspecialchars($back_link); ?>" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h1>Add Item</h1>
    </div>

    <!-- Add Item Container -->
    <div class="add-item-container">
        <button class="add-item-button" onclick="location.href='new_item.php'">
            New Item <i class="fas fa-plus"></i>
        </button>
        <button class="add-item-button" onclick="location.href='restock_item.php'">
            Restock Item <i class="fas fa-plus"></i>
        </button>
    </div>

</body>

</html>
