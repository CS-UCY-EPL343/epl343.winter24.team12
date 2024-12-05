<?php
session_start();

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

// Initialize success and error messages
$success = null;
$error = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include(__DIR__ . '/../../config/database.php');

    // Database connection
    $mysqli = Database::getConnection();

    // Set upload directory
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/HIMAROS/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create the uploads directory if it doesn't exist
    }

    $category = $_POST['category'];
    $itemName = $_POST['item_name'];
    $cost = $_POST['cost'];
    $itemType = $_POST['item_type'];
    $description = $_POST['description'];
    $minQuantity = $_POST['min_quantity'];
    $supplierID = $_POST['supplier_id'];

    // Check if the SupplierID exists
    $supplierCheckStmt = $mysqli->prepare("SELECT SupplierID FROM SUPPLIER WHERE SupplierID = ?");
    $supplierCheckStmt->bind_param("i", $supplierID);
    $supplierCheckStmt->execute();
    $supplierCheckStmt->store_result();

    if ($supplierCheckStmt->num_rows === 0) {
        $error = "Supplier ID does not exist. Please enter a valid Supplier ID.";
        $supplierCheckStmt->close();
    } else {
        $supplierCheckStmt->close();

        // Handle file upload
        $filename = uniqid() . '-' . basename($_FILES['item_image']['name']);
        $uploadFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['item_image']['tmp_name'], $uploadFile)) {
            $pictureURL = '/HIMAROS/uploads/' . $filename; // Save the relative URL to the database

            // Call the stored procedure to insert the item into the database
            $stmt = $mysqli->prepare("CALL AddNewItem(?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssssii', $category, $itemName, $pictureURL, $cost, $itemType, $description, $minQuantity, $supplierID);

            if ($stmt->execute()) {
                $success = "Successfully inserted a new item!";
            } else {
                $error = "Database Error: " . $stmt->error;
            }
        } else {
            $error = "Failed to upload the image. Please check the file permissions or directory.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Item</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
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

        /* Dashboard Container */
        .dashboard-container {
            margin-top: 100px;
            padding: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-container {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: left;
            font-size: 22px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }

        .form-group textarea {
            resize: vertical;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .button-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .button-container .cancel-btn {
            background-color: #f8d7da;
            color: #721c24;
        }

        .button-container .add-btn {
            background-color: #1a4f6e;
            color: white;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }


        .back-button {
           margin-top: 20px;
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

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <!-- Page Title -->
        <h1>Add New Item</h1>

        <!-- Add Item Form -->
        <div class="form-container">
            <h2>Item Details</h2>

            <?php if ($success): ?>
                <div class="message success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="message error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" name="category" id="category" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="item_name">Item Name</label>
                        <input type="text" name="item_name" id="item_name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="item_image">Item Image</label>
                        <input type="file" name="item_image" id="item_image" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="cost">Cost</label>
                        <input type="number" step="0.01" name="cost" id="cost" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="item_type">Item Type</label>
                        <select name="item_type" id="item_type" required>
                            <option value="" disabled selected>Select Item Type</option>
                            <option value="reusable">Reusable</option>
                            <option value="disposable">Disposable</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="min_quantity">Minimum Quantity</label>
                        <input type="number" name="min_quantity" id="min_quantity" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="supplier_id">Supplier ID</label>
                        <input type="number" name="supplier_id" id="supplier_id" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="4" required></textarea>
                    </div>
                </div>
                <div class="button-container">
                    <button type="reset" class="cancel-btn">Clear</button>
                    <button type="submit" class="add-btn">Add Item</button>
                </div>
            </form>
        </div>

        <!-- Back Button -->
        <button class="back-button" onclick="history.back();">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

</body>

</html>