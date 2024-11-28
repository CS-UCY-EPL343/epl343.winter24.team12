<?php
session_start();

include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin('doctor'); // Role = 'doctor'

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
while ($row = $result->fetch_assoc()) {
    $status = "In Stock";
    if ($row['Quantity'] == 0) {
        $status = "Out of Stock";
    } elseif ($row['Quantity'] < $row['Min_Quantity']) {
        $status = "Low Stock";
    } elseif ($row['Expiration_Date'] < date("Y-m-d")) {
        $status = "Expired";
    }
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

        /* Search Bar */
        .search-bar {
            margin-bottom: 20px; /* Add spacing between search bar and table */
            display: flex;
            justify-content: center; /* Center horizontally */
            width: 100%; /* Take full width for consistency */
        }

        .search-bar input {
            padding: 10px;
            width: 250px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center; /* Center the placeholder text */
        }

        /* Inventory Table */        
        .content {
            margin-left: 200px; /* Adjusted to ensure it clears the sidebar */
            flex-direction: column; /* Align items in a column */

            padding: 100px;
            display: right;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Ensures content takes full height */
            box-sizing: border-box;
        }

        table {
            width: auto; /* Adjusted to ensure the table content defines the width */
            border-collapse: collapse;
            margin: 0 auto; /* Centers the table horizontally */
            
        }


        table th,
        table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            
        }

        table th {
            background-color: #1a4f6e;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .status {
            
            font-weight: bold;
        }

        .status.in-stock {
            color: green;
        }

        .status.low-stock {
            color: orange;
        }

        .status.out-of-stock {
            color: red;
        }

        .status.expired {
            color: purple;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <strong>HIMAROS</strong>
        </div>
    </div>


    <!-- Left Side Navigation Bar -->
    <div class="sidebar">
        <a href="doc_dashboard.php" title="Dashboard">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="#" class="active" title="Inventory">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <a href="#" title="Operations">
            <i class="fas fa-stethoscope"></i> Operations
        </a>
        <a href="doc_supplier.php" title="Suppliers">
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

    <div class="content">
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
                        <th>Supplier_Name</th>
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

        searchInput.addEventListener("input", function () {
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