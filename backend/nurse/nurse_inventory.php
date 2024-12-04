<?php
session_start();

include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin('nurse'); // Role = 'nurse'

$mysqli = Database::getConnection();

// Fetch inventory items
$inventory_items = [];
$stmt = $mysqli->prepare("
    SELECT i.Item_Name, cs.Quantity, i.Min_Quantity, cs.Expiration_Date, s.Suppleir_Name
    FROM ITEM i
    JOIN CURRENT_STOCK cs ON i.ItemID = cs.ItemID
    JOIN SUPPLIER s ON i.SupplierID = s.SupplierID
");

$stmt->execute();
$result = $stmt->get_result();
$total_items = 0;
$in_stock = 0;
$low_stock = 0;
$out_of_stock = 0;

while ($row = $result->fetch_assoc()) {
    $status = "In Stock";
    if ($row['Quantity'] == 0) {
        $status = "Out of Stock";
        $out_of_stock++;
    } elseif ($row['Quantity'] < $row['Min_Quantity']) {
        $status = "Low Stock";
        $low_stock++;
    } else {
        $in_stock++;
    }
    $total_items++;
    $inventory_items[] = array_merge($row, ['Status' => $status]);
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
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
            margin-left: -10px;
            padding: 10px 1px;
            flex-direction: column;
        }

        /* Search Bar Adjustments */
        .search-bar {
            width: 80%;
            /* Same width as the progress bar */
            margin: 20px auto;
            margin-left: 9%;
            /* Center it */
        }

        .search-bar input {
            width: 100%;
            /* Full width of the parent container */
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            /* Center-align text */
        }

        /* Summary Section */
        .summary-section {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .summary-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f0f0f0;
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 80%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Progress Bar */
        .progress-bar-container {
            width: 100%;
            max-width: 100%;
            height: 30px;
            margin: 20px 0;
            border-radius: 5px;
            overflow: hidden;
            background-color: #e9ecef;
            display: flex;
        }

        .progress-bar {
            height: 100%;
            line-height: 30px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            text-align: center;
        }

        .progress-bar.in-stock {
            background-color: #28a745;
            width: <?php echo ($in_stock / max(1, $total_items)) * 100; ?>%;
        }

        .progress-bar.low-stock {
            background-color: #ffc107;
            width: <?php echo ($low_stock / max(1, $total_items)) * 100; ?>%;
        }

        .progress-bar.out-of-stock {
            background-color: #dc3545;
            width: <?php echo ($out_of_stock / max(1, $total_items)) * 100; ?>%;
        }

        .stock-status {
            font-size: 14px;
            font-weight: bold;
        }

        .scan-box {
            background-color: #216491;
            color: white;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 80%;
            height: 50%
                /* Match the progress bar width */
                max-width: 100%;
            margin: 10px auto;
            /* Center the box */
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }

        .scan-section {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .view-item-btn {
            display: block;
            background-color: #216491;
            /* Bootstrap primary blue */
            color: white;
            font-size: 18px;
            font-weight: bold;
            padding: 15px;
            text-align: center;
            border: 2px solid white;
            border-radius: 8px;
            width: 80%;
            /* Same width as the progress bar */
            margin: 20px auto;
            /* Center it */
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .view-item-btn:hover {
            background-color: #0056b3;
            /* Darker blue for hover */
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        /* Inventory Table */
        .content {
            margin-left: 200px;
            padding: 100px;
        }

        /* Table Adjustments */
        table {
            width: 100%;
            /* Full width */
            margin: 20px 0;
            border-collapse: collapse;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        table th,
        table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        table th {
            background-color: #1a4f6e;
            color: white;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
            /* Subtle highlight */
        }

        /* Content Section */
        .content {
            margin-left: 200px;
            padding: 80px 20px 20px;
            /* Add padding to ensure title is not overlapped by navbar */
            width: calc(100% - 200px);
            /* Ensures table and other content stretch across the remaining space */
            box-sizing: border-box;
            /* Prevents content from exceeding boundaries */
        }

        .title-section h1 {
            font-size: 28px;
            color: #1a4f6e;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
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
        <a href="nurse_dashboard.php" title="Dashboard"><i class="fas fa-home"></i> Dashboard</a>
        <a href="#" class="active" title="Inventory"><i class="fas fa-boxes"></i> Inventory</a>
        <a href="nurse_operations.php" title="Operations"><i class="fas fa-stethoscope"></i> Operations</a>
        <a href="nurse_supplier.php" title="Suppliers"><i class="fas fa-truck"></i> Suppliers</a>
        <a href="nurse_reports.php" title="Reports"><i class="fas fa-chart-line"></i> Reports</a>
        <a href="#" title="Users"><i class="fas fa-users"></i> Users</a>
        <a href="nurse_settings.php" title="Settings"><i class="fas fa-cog"></i> Settings</a>
    </div>

    <div class="content">
        <h1>Inventory </h1>

        <!-- Summary Section -->
        <div class="summary-section">
            <!-- Product Statistics -->
            <div class="summary-bar">
                <div class="progress-bar-container">
                    <div class="progress-bar in-stock">In : <?php echo $in_stock; ?></div>
                    <div class="progress-bar low-stock">Low : <?php echo $low_stock; ?></div>
                    <div class="progress-bar out-of-stock">Out : <?php echo $out_of_stock; ?></div>
                </div>
                <p style="text-align: center; font-size: 16px; color: #555;">
                    Total: <?php echo $total_items; ?> Products
                </p>
                <div class="stock-status">
                    In Stock: <?php echo $in_stock; ?> |
                    Low Stock: <?php echo $low_stock; ?> |
                    Out of Stock: <?php echo $out_of_stock; ?>
                </div>
            </div>

            <div class="scan-box">
                <button class="view-item-btn" onclick="location.href='../common/view_item_info.php';">
                    View Item Info
                </button>
                <button class="view-item-btn" onclick="location.href='../common/add_item.php';">
                    Add Item
                </button>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search items...">
            </div>


            <!-- Inventory Table -->
            <div class="table">
                <table id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Min Quantity</th>
                            <th>Supplier Name</th>
                            <th>Expiration Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['Item_Name']); ?></td>
                                <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['Min_Quantity']); ?></td>
                                <td><?php echo htmlspecialchars($item['Suppleir_Name']); ?></td>
                                <td><?php echo htmlspecialchars($item['Expiration_Date']); ?></td>
                                <td class="status <?php echo strtolower(str_replace(' ', '-', $item['Status'])); ?>">
                                    <?php echo htmlspecialchars($item['Status']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($inventory_items)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No items found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            // Sort table alphabetically by the first column (Item Name)
            function sortTableAlphabetically() {
                const table = document.getElementById("inventoryTable");
                const rows = Array.from(table.querySelectorAll("tbody tr"));

                rows.sort((rowA, rowB) => {
                    const nameA = rowA.querySelector("td:first-child").textContent.toLowerCase();
                    const nameB = rowB.querySelector("td:first-child").textContent.toLowerCase();
                    return nameA.localeCompare(nameB);
                });

                const tbody = table.querySelector("tbody");
                rows.forEach(row => tbody.appendChild(row));
            }

            // Filter table rows based on search input
            const searchInput = document.getElementById("searchInput");
            const tableRows = document.querySelectorAll("#inventoryTable tbody tr");

            searchInput.addEventListener("input", function() {
                const searchValue = this.value.toLowerCase();
                tableRows.forEach(row => {
                    const itemName = row.querySelector("td:first-child").textContent.toLowerCase();
                    row.style.display = itemName.includes(searchValue) ? "" : "none";
                });
            });

            // Call the sorting function when the page loads
            document.addEventListener("DOMContentLoaded", sortTableAlphabetically);
        </script>
</body>

</html>
