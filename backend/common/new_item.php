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

        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #1a4f6e;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
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

        .add-item-form {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .add-item-form h2 {
            margin-bottom: 20px;
            font-size: 22px;
            color: #333;
            text-align: center;
        }

        .add-item-form input,
        .add-item-form select,
        .add-item-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
        }

        .add-item-form button {
            display: block;
            margin: 20px auto;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            background-color: #216491;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .add-item-form button:hover {
            background-color: #1a4f6e;
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 16px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>

    <div class="top-bar">
        <a href="<?php echo htmlspecialchars($back_link); ?>" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h1>Add New Item</h1>
    </div>

    <div class="add-item-form">
        <h2>Item Information</h2>

        <?php if ($success): ?>
            <div class="message success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="text" name="category" placeholder="Category" required>
            <input type="text" name="item_name" placeholder="Item Name" required>
            <input type="file" name="item_image" accept="image/*" required>
            <input type="number" step="0.01" name="cost" placeholder="Cost" required>
            <select name="item_type" required>
                <option value="" disabled selected>Select Item Type</option>
                <option value="reusable">Reusable</option>
                <option value="disposable">Disposable</option>
                <option value="other">Other</option>
            </select>
            <textarea name="description" rows="4" placeholder="Description" required></textarea>
            <input type="number" name="min_quantity" placeholder="Minimum Quantity" required>
            <input type="number" name="supplier_id" placeholder="Supplier ID" required>
            <button type="submit">Add Item <i class="fas fa-plus"></i></button>
        </form>
    </div>

</body>

</html>
