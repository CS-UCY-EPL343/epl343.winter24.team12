<?php
session_start();

include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin('doctor'); // Role = 'doctor'

$mysqli = Database::getConnection();

// Data for the cards
$out_of_stock = [];
$low_stock = [];
$expired = [];

// Fetch out-of-stock items
$stmt = $mysqli->prepare("
    SELECT i.Item_Name 
    FROM ITEM i
    JOIN CURRENT_STOCK cs ON i.ItemID = cs.ItemID
    WHERE cs.Quantity = 0
    LIMIT 10
");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $out_of_stock[] = $row['Item_Name'];
}
$stmt->close();

// Fetch low-stock items (grouped by item, excluding expired items)
$stmt = $mysqli->prepare("
    SELECT i.Item_Name, SUM(cs.Quantity) AS Total_Quantity
    FROM ITEM i
    JOIN CURRENT_STOCK cs ON i.ItemID = cs.ItemID
    WHERE cs.Quantity > 0 AND cs.Quantity < i.Min_Quantity AND cs.Expiration_Date >= CURDATE()
    GROUP BY i.Item_Name
    LIMIT 10
");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $low_stock[] = $row['Item_Name'] . " (Qty: " . $row['Total_Quantity'] . ")";
}
$stmt->close();


// Fetch expired items
$stmt = $mysqli->prepare("
    SELECT i.Item_Name, cs.Expiration_Date
    FROM ITEM i
    JOIN CURRENT_STOCK cs ON i.ItemID = cs.ItemID
    WHERE cs.Expiration_Date < CURDATE()
    LIMIT 10
");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $expired[] = $row['Item_Name'] . " (Expired: " . $row['Expiration_Date'] . ")";
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Include Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
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

        /* Left-Side Navigation Bar */
        .sidebar {
            width: 180px;
            background-color: #1a4f6e;
            height: 100vh;
            position: fixed;
            top: 60px;
            left: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
            padding-left: 20px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            gap: 10px;
            padding: 10px 0;
            width: 100%;
        }

        .sidebar a:hover {
            background-color: #216491;
            border-radius: 10px;
        }

        .sidebar a.active {
            background-color: #ffc107;
            color: #1a4f6e;
            font-weight: bold;
            border-radius: 5px;
            margin-left: -10px; /* Added margin to shift it slightly left */
            padding: 10px 1px;
            flex-direction: column;
        }

        .sidebar i {
            font-size: 18px;
        }

        /* Dashboard Container */
        .dashboard-container {
            margin-top: 80px;
            margin-left: 200px; /* Adjusted for sidebar width */
            padding: 20px;
            width: calc(100% - 200px);
        }

        /* Scan Section */
        .scan-box {
            background-color: #216491;
            color: white;
            border-radius: 8px;
            padding: 40px;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 35px;
        }

        .scan-section {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
            justify-content: center;
        }

        .scan-section i {
            font-size: 70px;
            color: white;
            position: absolute;
            left: 250px;
        }

        .scan-section button {
            background-color: #216491;
            color: white;
            padding: 15px 80px;
            border: 2px solid white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 26px;
            font-weight: bold;
            text-align: center;
        }

        .scan-section button:hover {
            background-color: #1a4f6e;
            border-color: #ffc107;
        }

        /* Cards Section */
        .cards-outer-box {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .cards-container {
            display: flex;
            justify-content: space-around;
            gap: 20px;
        }

        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 30%;
            padding: 20px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            height: 200px;
            overflow-y: auto;
        }

        .card h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .card ul {
            list-style-type: decimal;
            padding-left: 20px;
            margin: 0;
        }

        .card ul li {
            padding: 5px 0;
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

    <!-- Left Side Navigation Bar -->
    <div class="sidebar">
        <a href="#" class="active" title="Dashboard">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="doc_inventory.php" title="Inventory">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <a href="doc_operations.php" title="Operations">
            <i class="fas fa-stethoscope"></i> Operations
        </a>
        <a href="doc_supplier.php" title="Suppliers">
            <i class="fas fa-truck"></i> Suppliers
        </a>
        <a href="doc_reports.php" title="Reports">
            <i class="fas fa-chart-line"></i> Reports
        </a>
        <a href="doc_users.php" title="Users">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="doc_settings.php" title="Settings">
            <i class="fas fa-cog"></i> Settings
        </a>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <h1>Dashboard</h1>

        <!-- Scan Section -->
        <div class="scan-box">
            <div class="scan-section">
                <i class="fas fa-qrcode"></i>
                <div>
                    <!-- Button to View Item Info -->
                    <button onclick="location.href='../common/view_item_info.php';" style="margin-right: 15px;">View Item Info</button>
                    <!-- Button to Use Item -->
                    <button onclick="location.href='../common/use_item.php';">Use Item</button>
                </div>
            </div>
        </div>

        <!-- Cards Section -->
        <div class="cards-outer-box">
            <div class="cards-container">
                <!-- Out of Stock -->
                <div class="card">
                    <h3>Out of Stock</h3>
                    <ul>
                        <?php foreach ($out_of_stock as $item): ?>
                            <li><?php echo htmlspecialchars($item); ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($out_of_stock)): ?>
                            <li>No items</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Low Stock -->
                <div class="card">
                    <h3>Low Stock</h3>
                    <ul>
                        <?php foreach ($low_stock as $item): ?>
                            <li><?php echo htmlspecialchars($item); ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($low_stock)): ?>
                            <li>No items</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Expired -->
                <div class="card">
                    <h3>Expired</h3>
                    <ul>
                        <?php foreach ($expired as $item): ?>
                            <li><?php echo htmlspecialchars($item); ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($expired)): ?>
                            <li>No items</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
