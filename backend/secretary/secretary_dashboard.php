<?php
session_start();

include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin('secretary'); // Role = 'secretary'


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

        .cards-container {
            display: flex;
            justify-content: space-around;
            gap: 20px;
        }

        /* Cards Section */
        .cards-outer-box {
            display: flex; /* Use flexbox for layout */
            justify-content: space-around; /* Space the cards evenly */
            gap: 20px; /* Add some spacing between cards */
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap; /* Allows wrapping if there isn't enough space */
        }

        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            flex: 1; /* Make all cards equal width */
            min-width: 250px; /* Ensure a minimum width for smaller screens */
            max-width: 30%; /* Limit maximum width for larger screens */
            padding: 20px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .card table {
            width: 100%;
            border-collapse: collapse;
        }

        .card table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .card table td:last-child {
            text-align: right;
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
        </div>
        <!-- Add Logout Icon -->
        <a href="../common/logout.php" title="Logout" style="color: white; text-decoration: none; margin-left: 15px;">
            <i class="fas fa-sign-out-alt"></i>
        </a>  
    </div>

    <!-- Left Side Navigation Bar -->
    <div class="sidebar">
        <a href="#" class="active" title="Dashboard">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="#" title="Inventory">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <a href="#" title="Operations">
            <i class="fas fa-stethoscope"></i> Operations
        </a>
        <a href="#" title="Suppliers">
            <i class="fas fa-truck"></i> Suppliers
        </a>
        <a href="#" title="Reports">
            <i class="fas fa-chart-line"></i> Reports
        </a>
        <a href="#" title="Users">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="#" title="Settings">
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
                    <button onclick="location.href='../common/view_item_info.php';">View Item Info</button>
                    <!-- Button to Use Item -->
                    <button onclick="location.href='../common/use_item.php';" style="margin-left: 15px;">Use Item</button>
                </div>
            </div>
        </div>

    <!-- Out-of-Stock Items -->
    <div class="cards-outer-box">
        <div class="card">
            <h3 style="color: #E63946">Out-of-Stock Items</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <?php foreach ($out_of_stock as $item): ?>
                        <tr>
                            <td style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">
                                <?php echo htmlspecialchars($item); ?>
                            </td>
                            <td style="text-align: right; padding: 8px; border-bottom: 1px solid #ddd;">
                                <button onclick="location.href='../common/full_item_info.php?item=<?php echo urlencode($item); ?>';" style="padding: 5px 10px; font-size: 14px; border-radius: 4px; border: 1px solid #ccc; cursor: pointer; background-color: #1a4f6e; color: white;">View Full Info</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Low-Stock Items -->
        <div class="card">
            <h3 style="color: #E9B949">Low-Stock Items</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <?php foreach ($low_stock as $item): 
                        // Split the name and quantity
                        $parts = explode(" (Qty: ", $item);
                        $item_name = $parts[0]; // Clean item name
                    ?>
                        <tr>
                            <td style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">
                                <?php echo htmlspecialchars($item); ?>
                            </td>
                            <td style="text-align: right; padding: 8px; border-bottom: 1px solid #ddd;">
                                <button onclick="location.href='../common/full_item_info.php?item=<?php echo urlencode($item_name); ?>';" style="padding: 5px 10px; font-size: 14px; border-radius: 4px; border: 1px solid #ccc; cursor: pointer; background-color: #1a4f6e; color: white;">View Full Info</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Expired Items -->
        <div class="card">
            <h3 style="color: #D9822B">Expired Items</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <?php foreach ($expired as $item): 
                        // Split the name and expiration
                        $parts = explode(" (Expired: ", $item);
                        $item_name = $parts[0]; // Clean item name
                    ?>
                        <tr>
                            <td style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">
                                <?php echo htmlspecialchars($item); ?>
                            </td>
                            <td style="text-align: right; padding: 8px; border-bottom: 1px solid #ddd;">
                                <button onclick="location.href='../common/full_item_info.php?item=<?php echo urlencode($item_name); ?>';" style="padding: 5px 10px; font-size: 14px; border-radius: 4px; border: 1px solid #ccc; cursor: pointer; background-color: #1a4f6e; color: white;">View Full Info</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
</body>

</html>

