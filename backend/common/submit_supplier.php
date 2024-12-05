<?php
session_start();
include(__DIR__ . '/../../config/database.php');

// Ensure the user is logged in as admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Database connection
$mysqli = Database::getConnection();

// Initialize variables
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplierName = $_POST['supplier_name'];
    $contactInfo = $_POST['contact_info'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Use the stored procedure to add a new supplier
        $stmt = $mysqli->prepare("CALL AddSupplier(?, ?, ?, ?)");
        $stmt->bind_param('ssss', $supplierName, $contactInfo, $email, $address);

        if ($stmt->execute()) {
            $success = "Supplier successfully added!";
        } else {
            $error = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
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

        .add-supplier-container {
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            text-align: center;
        }

        .add-supplier-container h2 {
            margin: 0 0 20px;
            font-size: 22px;
            color: #333;
        }

        .add-supplier-form input,
        .add-supplier-form select,
        .add-supplier-form button {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            color: #333;
        }

        .add-supplier-form button {
            background-color: #1a4f6e;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .add-supplier-form button:hover {
            background-color: #155bb5;
        }
    </style>
</head>

<body>
    <div class="add-supplier-container">
        <h2>Add New Supplier</h2>

        <?php if ($success): ?>
            <div class="message success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form class="add-supplier-form" action="" method="POST">
            <input type="text" name="supplier_name" placeholder="Supplier Name" required>
            <input type="text" name="contact_info" placeholder="Contact Info" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="address" placeholder="Supplier Address" required>
            <button type="submit">Add Supplier</button>
        </form>
    </div>
</body>

</html>
