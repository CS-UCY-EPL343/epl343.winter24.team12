<?php
session_start();

include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin('nurse'); // Role = 'nurse'

$mysqli = Database::getConnection();

// Fetch supplier details
$suppliers = [];
$stmt = $mysqli->prepare("
    SELECT Suppleir_Name, Contact_Info, Email, Supplier_Address
    FROM SUPPLIER
");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
        }

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

        .content {
            margin-left: 200px;
            padding: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar input {
            padding: 10px;
            width: 250px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
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
    </div>

    <!-- Left Side Navigation Bar -->
    <div class="sidebar">
        <a href="nurse_dashboard.php" title="Dashboard">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="nurse_inventory.php" title="Inventory">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <a href="nurse_operations.php" title="Operations">
            <i class="fas fa-stethoscope"></i> Operations
        </a>
        <a href="nurse_suppliers.php" class="active" title="Suppliers">
            <i class="fas fa-truck"></i> Suppliers
        </a>
        <a href="nurse_reports.php" title="Reports">
            <i class="fas fa-chart-line"></i> Reports
        </a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search suppliers...">
        </div>

        <!-- Suppliers Table -->
        <table id="supplierTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Info</th>
                    <th>Email</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($supplier['Suppleir_Name']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['Contact_Info']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['Email']); ?></td>
                        <td><?php echo htmlspecialchars($supplier['Supplier_Address']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($suppliers)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No suppliers found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript for Search -->
    <script>
        const searchInput = document.getElementById("searchInput");
        const tableRows = document.querySelectorAll("#supplierTable tbody tr");

        searchInput.addEventListener("input", function () {
            const searchValue = this.value.toLowerCase();
            tableRows.forEach(row => {
                const supplierName = row.querySelector("td:nth-child(1)").textContent.toLowerCase();
                if (supplierName.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    </script>
</body>

</html>
