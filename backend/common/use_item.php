<?php
session_start();

// check if the user is logged in and their role is set
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php"); // redirect to login page if not logged in
    exit();
}

// get the user's role
$role = $_SESSION['role'];

// define dashboard links for each role
$dashboard_links = [
    'admin' => '../admin/admin_dashboard.php',
    'doctor' => '../doctor/doc_dashboard.php',
    'nurse' => '../nurse/nurse_dashboard.php',
    'secretary' => '../secretary/secretary_dashboard.php',
];

// determine the correct dashboard link
$back_link = isset($dashboard_links[$role]) ? $dashboard_links[$role] : '../login.php';

// clear session data if "Done" button is clicked
if (isset($_POST['clear_session'])) {
    unset($_SESSION['scanned_items']);
    header("Location: $back_link");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Use Item</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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

        .dashboard-container {
            margin: 120px auto;
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

        .scan-box {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .scan-section label {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .scan-section input {
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
            max-width: 400px;
            margin: 10px 0;
        }

        .scan-section button {
            background-color: #1a4f6e;
            color: white;
            padding: 10px 30px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .scan-section button:hover {
            background-color: #155bb5;
        }

        .scanned-items-box {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            width: 93%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
            max-width: 400px;
        }

        li {
            padding: 15px;
            border: 1px solid #d9d9d9;
            border-radius: 5px;
            background-color: white;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .remove-button {
            background-color: #ff5e57;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
            float: right;
        }

        .remove-button:hover {
            background-color: #e04e47;
        }

        .action-buttons {
            margin-top: 30px;
            text-align: left;
        }

        .action-buttons .back-button,
        .action-buttons .done-button {
            background-color: #1a4f6e;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .action-buttons .back-button:hover,
        .action-buttons .done-button:hover {
            background-color: #155bb5;
        }

        .action-buttons .back-button {
            margin-right: 10px;
        }

        .action-buttons i {
            margin-right: 8px;
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

    <div class="dashboard-container">
        <!-- Title -->
        <h1>Use Item</h1>
        <!-- scan box -->
        <div class="scan-box">
            <div class="scan-section">
                <form method="POST" action="process_barcode.php" class="scan-section">
                    <label for="barcode">Enter Barcode:</label>
                    <input type="text" id="barcode" name="barcode" placeholder="Scan barcode here..." required>
                    <input type="hidden" name="action" value="use">
                    <button type="submit"><i class="fas fa-box"></i> Use Item</button>
                </form>
            </div>
        </div>

        <!-- Scanned Items List -->
        <h2 style="text-align: left; margin-bottom: 10px; margin-left: 10px;">Scanned Items</h2>
        <div class="scanned-items-box">
            <ul>
                <?php
                if (isset($_SESSION['scanned_items']) && count($_SESSION['scanned_items']) > 0) {
                    foreach ($_SESSION['scanned_items'] as $index => $item) {
                        echo "<li>";
                        echo "<strong>Item Name:</strong> " . htmlspecialchars($item['Item_Name']) . "<br>";
                        echo "<strong>Quantity Remaining:</strong> " . htmlspecialchars($item['Quantity']) . "<br>";
                        echo "<strong>Expiration Date:</strong> " . htmlspecialchars($item['Expiration_Date']) . "<br>";
                        echo "<strong>Supplier:</strong> " . htmlspecialchars($item['Suppleir_Name']) . "<br>";
                        echo "<form method='POST' action='remove_item.php'>
                        <input type='hidden' name='item_index' value='$index'>
                        <button class='remove-button' type='submit'>Remove</button>
                      </form>";
                        echo "</li>";
                    }
                } else {
                    echo "<li>No items scanned yet.</li>";
                }
                ?>
            </ul>
        </div>

        <!-- Action Buttons -->
        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="back-button" onclick="location.href='<?php echo $back_link; ?>'">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <form method="POST" style="display: inline;">
                <button class="done-button" name="clear_session">
                    <i class="fas fa-check"></i> Done
                </button>
            </form>
        </div>
    </div>
</body>

</html>