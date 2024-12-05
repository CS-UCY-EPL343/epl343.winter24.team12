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
            margin: 120px auto;
            max-width: 600px;
            padding: 20px;
        }

        h1 {
            color: black;
            margin-bottom: 30px;
            font-size: 28px;
            text-align: left;
            font-weight: bold;
        }

        .add-supplier-container {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .add-supplier-container h2 {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-align: left;
        }

        .add-supplier-form input,
        .add-supplier-form button {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            color: #333;
            box-sizing: border-box;
        }

        .add-supplier-form input {
            background-color: #f9f9f9;
        }

        .add-supplier-form button {
            background-color: #1a4f6e;
            color: white;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .add-supplier-form button:hover {
            background-color: #155bb5;
            transform: scale(1.02);
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

        /* Back Button */
        .back-button {
            margin-top: 20px;
            display: inline-flex;
            align-items: center;
            padding: 15px;
            background-color: #1a4f6e;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-button i {
            margin-right: 10px;
        }

        .back-button:hover {
            background-color: #155bb5;
            transform: scale(1.02);
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


    <<!-- Dashboard Content -->
        <div class="dashboard-container">
            <!-- Page Title -->
            <h1>Add New Supplier</h1>

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
            <!-- Back Button -->
            <a href="<?php echo $back_link; ?>" class="back-button">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

</body>

</html>