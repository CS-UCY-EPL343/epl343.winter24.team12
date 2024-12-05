<?php
session_start();  // Ensure session is started
include(__DIR__ . '/../../config/database.php');

// Check if the user is logged in and their role is set
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the user's role
$role = $_SESSION['role'];

// Define dashboard links for each role
$dashboard_links = [
    'admin' => '../admin/admin_supplier.php',
    'doctor' => '../doctor/doc_supplier.php',
    'nurse' => '../nurse/nurse_supplier.php',
    'secretary' => '../secretary/secretary_supplier.php',
];

// Ensure that we have a valid role and redirect to the appropriate dashboard
if (isset($dashboard_links[$role])) {
    $back_link = $dashboard_links[$role];
} else {
    $back_link = '../login.php';  // Default to login page if the role is unknown
}

// Database connection
$mysqli = Database::getConnection();
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
    <title>Add New Supplier</title>
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

        /* Add Supplier Form Container */
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

    <!-- Top Bar -->
    <div class="top-bar">
        <a href="<?php echo htmlspecialchars($back_link); ?>" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h1>Add New Supplier</h1>
    </div>

    <!-- Add Supplier Form -->
    <div class="add-supplier-container">
        <h2>Supplier Information</h2>

        <?php if ($success): ?>
            <div class="message success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form class="add-supplier-form" method="POST">
            <input type="text" name="supplier_name" placeholder="Supplier Name" required>
            <input type="text" name="contact_info" placeholder="Contact Info" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="address" placeholder="Supplier Address" required>
            <button type="submit">Add Supplier</button>
        </form>
    </div>

</body>

</html>
