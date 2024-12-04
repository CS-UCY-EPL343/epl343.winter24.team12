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
    <title>Operation/Add new</title>
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
            /* Added margin to shift it slightly left */
            padding: 10px 1px;
            flex-direction: column;
        }

        .sidebar i {
            font-size: 18px;
        }

        /* Dashboard Container */
        .dashboard-container {
            margin-top: 80px;
            margin-left: 200px;
            /* Adjusted for sidebar width */
            padding: 20px;
            width: calc(100% - 200px);
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

        .operations-dropdown a {
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
            padding-left: 15px;
            margin-top: 5px;
        }

        .operations-dropdown .dropdown-content a {
            font-size: 14px;
            color: white;
            text-decoration: none;
            padding: 5px 10PX;
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
            /* Yellow color for active */
            font-weight: bold;
        }


        .form-container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: left;
        }

        .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            flex: 1;
            font-weight: bold;
            margin-right: 10px;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            flex: 2;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            gap: 15px;
        }

        .button-container button {
            padding: 15px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .button-container .clear-btn {
            background-color: #f8d7da;
            color: #721c24;
        }

        .button-container .cancel-btn {
            background-color: #f4f4f4;
            color: #333;
            border: 1px solid #ccc;
        }

        .button-container .add-btn {
            background-color: #1a4f6e;
            color: white;
        }
    </style>
</head>

<>
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

    <div class="sidebar">
        <a href="doc_dashboard.php" title="Dashboard">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="doc_inventory.php" title="Inventory">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <!-- Single "Operations" with Dropdown -->
        <div class="operations-dropdown open">
        <a href="doc_operations.php" class="active" title="Operations" onclick="toggleDropdown(event)" style="background-color: #ffc107; color: #1a4f6e; font-size: 18px; font-weight: bold; padding: 10px; border-radius: 5px;">
            <i class="fas fa-stethoscope"></i> Operations <i class="fas fa-chevron-down dropdown-arrow"></i>
        </a>
        <div class="dropdown-content">
            <a href="doc_operations.php.php" title="Add New" style="color: #ffc107;">> Add New</a>
            <a href="doc_history_operations.php" title="History">> History</a>
        </div>
    </div>
        <a href="doc_supplier.php" title="Suppliers">
            <i class="fas fa-truck"></i> Suppliers
        </a>

        <a href="doc_reports.php" title="Reports">
            <i class="fas fa-chart-line"></i> Reports
        </a>
        <a href="#" title="Users">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="doc_settings.php" title="Settings">
            <i class="fas fa-cog"></i> Settings
        </a>


    </div>



    <script>
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.target.closest('.operations-dropdown');
            dropdown.classList.toggle('open');
        }
    </script>


    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <h1>Operations / Add New</h1>
        <div class="form-container">
            <h2>Add New Operation Details</h2>
            <form>
                <div class="form-group">
                    <label for="patient-name">Patient Name:</label>
                    <input type="text" id="patient-name" placeholder="Enter patient name">
                </div>
                <div class="form-group">
                    <label for="patient-id">Patient ID:</label>
                    <input type="text" id="patient-id" placeholder="Enter patient ID">
                </div>
                <div class="form-group">
                    <label for="operation-room">Operation Room:</label>
                    <input type="text" id="operation-room" placeholder="Enter operation room">
                </div>
                <div class="form-group">
                    <label for="operation-id">Operation ID:</label>
                    <input type="text" id="operation-id" placeholder="Enter operation ID">
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date">
                </div>
                <div class="form-group">
                    <label for="operation-type">Operation Type:</label>
                    <input type="text" id="operation-type" placeholder="Enter operation type">
                </div>
                <div class="form-group">
                    <label for="time-started">Time Started:</label>
                    <input type="time" id="time-started">
                </div>
                <div class="form-group">
                    <label for="time-finished">Time Finished:</label>
                    <input type="time" id="time-finished">
                </div>
                <div class="form-group">
                    <label for="duration">Duration:</label>
                    <input type="text" id="duration" placeholder="Enter duration">
                </div>
                <div class="form-group">
                    <label for="main-doctor">Main Doctor:</label>
                    <input type="text" id="main-doctor" placeholder="Enter main doctor">
                </div>
                <div class="form-group">
                    <label for="assistant-doctors">Assistant Doctors:</label>
                    <input type="text" id="assistant-doctors" placeholder="Enter assistant doctors">
                </div>
                <div class="form-group">
                    <label for="nurses">Nurses:</label>
                    <input type="text" id="nurses" placeholder="Enter nurses">
                </div>
                <div class="form-group">
                    <label for="items-used">Items Used:</label>
                    <input type="text" id="items-used" placeholder="Enter items used">
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" rows="4" placeholder="Enter description"></textarea>
                </div>

                <div class="button-container">
                    <button type="reset" class="clear-btn">Clear Text</button>
                    <button type="button" class="cancel-btn" onclick="window.location.href='dashboard.php';">Cancel</button>
                    <button type="submit" class="add-btn">Add</button>
                </div>
            </form>
        </div>
    </div>
    </body>

</html>