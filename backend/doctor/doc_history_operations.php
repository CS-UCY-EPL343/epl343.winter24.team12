<?php
session_start();

include(__DIR__ . '/../../config/database.php');
include('../assets/inc/checklogin.php');
checklogin('doctor'); // Role = 'doctor'

$mysqli = Database::getConnection();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operation/History</title>
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
            margin-left: -10px;
            padding: 10px 1px;
            flex-direction: column;
        }

        .sidebar i {
            font-size: 18px;
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




        .operations-dropdown {
            width: 90%;
        }

        .operations-dropdown > a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffc107;
            color: #1a4f6e;
            font-size: 14px;
            font-weight: bold;
            padding: 5px 7px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .operations-dropdown .dropdown-arrow {
            font-size: 12px;
            transition: transform 0.3s;
        }

        .operations-dropdown .dropdown-content {
            display: none;
            flex-direction: column;
            margin-top: 5px;
            padding-left: 15px;
        }

        .operations-dropdown .dropdown-content a {
            font-size: 14px;
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            transition: color 0.2s;
            background-color: transparent;
        }

        .operations-dropdown .dropdown-content a:hover {
            color: #ffc107;
        }

        .operations-dropdown.open .dropdown-content {
            display: flex;
        }

        .operations-dropdown.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        .operations-dropdown .dropdown-content a.active {
            color: #ffc107;
            font-weight: bold;
        }

        /* Dashboard Container */
        .dashboard-container {
            margin-top: 80px;
            margin-left: 200px;
            padding: 20px;
            width: calc(100% - 200px);
        }

        /* Table Styles */
        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        table .view-report-btn {
            background-color: #1a4f6e;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
            text-decoration: none;
        }

        table .view-report-btn:hover {
            background-color: #ffc107;
            color: #1a4f6e;
        }

        .filter-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 10px;
        }

        .filter-container i {
            margin-left: 5px;
            color: #1a4f6e;
            cursor: pointer;
        }

        .create-report-btn {
            background-color: #1a4f6e;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .create-report-btn:hover {
            background-color: #ffc107;
            color: #1a4f6e;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <strong>HIMAROS</strong>
        </div>
        <div class="icons">
            <i class="fas fa-folder" title="Files"></i>
            <i class="fas fa-cog" title="Settings"></i>
            <i class="fas fa-user-circle" title="Profile"></i>
            <a href="../common/logout.php" title="Logout" style="color: white; text-decoration: none; margin-left: 15px;">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <div class="sidebar">
        <a href="doc_dashboard.php" title="Dashboard">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="doc_inventory.php" title="Inventory">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <div class="operations-dropdown open">
        <a href="#" class="active" title="Operations" onclick="toggleDropdown(event)" style="background-color: #ffc107; color: #1a4f6e; font-size: 18px; font-weight: bold; padding: 10px; border-radius: 5px;">
        <i class="fas fa-stethoscope"></i> Operations <i class="fas fa-chevron-down dropdown-arrow"></i>
        </a>
            <div class="dropdown-content">
                <a href="doc_operations.php" title="Add New">> Add New</a>
                <a href="doc_history_operations.php" title="History" style = "color: #ffc107;">> History</a>
            </div>
        </div>
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

    <div class="dashboard-container">
        <h1>Operation / History</h1>
        <div class="filter-container">
            <span>Filter</span>
            <i class="fas fa-filter"></i>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>A/A</th>
                        <th>Patient Name</th>
                        <th>Operation Type</th>
                        <th>Operation ID</th>
                        <th>Date</th>
                        <th>Duration</th>
                        <th>Main Doctor</th>
                        <th>View Report</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>Surgery</td>
                        <td>OP123</td>
                        <td>2024-12-01</td>
                        <td>2 hours</td>
                        <td>Dr. Smith</td>
                        <td><a href="doc_reports.php" class="view-report-btn">View Report</a></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jane Doe</td>
                        <td>Consultation</td>
                        <td>OP124</td>
                        <td>2024-12-02</td>
                        <td>1 hour</td>
                        <td>Dr. Brown</td>
                        <td><a href="doc_reports.php" class="view-report-btn">View Report</a></td>
                    </tr>
                </tbody>
            </table>
            <button class="create-report-btn" onclick="location.href='doc_reports.php'">CREATE REPORT FOR ALL</button>
        </div>
    </div>

    <script>
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.target.closest('.operations-dropdown');
            dropdown.classList.toggle('open');
        }
    </script>
</body>

</html>
