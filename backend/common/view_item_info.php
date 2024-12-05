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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Item Info</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
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

        .dashboard-container {
            margin: 100px auto;
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
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .scan-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            width: 100%;
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
        }

        .scan-section button {
            background-color: #1a4f6e;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .scan-section button:hover {
            background-color: #155bb5;
        }

        #item-info {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #ffffff;
            color: #333;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .error {
            color: #e74c3c;
            font-weight: bold;
        }

        /* Back Button */
        .back-button {
            background-color: #1a4f6e;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #155bb5;
        }

        .back-button i {
            font-size: 14px;
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



    </div>
    <div class="dashboard-container">
        <h1>View Item Information</h1>



        <!-- scan box -->
        <div class="scan-box">
            <div class="scan-section">
                <label for="barcode">Enter Barcode:</label>
                <input type="text" id="barcode" name="barcode" placeholder="Scan barcode here..." required>
                <button id="submit-button">Submit</button>
            </div>
        </div>

        <!-- container to display item info or error -->
        <div id="item-info"></div>

        <!-- Back Button -->
        <button class="back-button" onclick="history.back();">
            <i class="fas fa-arrow-left"></i> Back
        </button>

    </div>
    <script>
        document.getElementById('submit-button').addEventListener('click', function(e) {
            const barcode = document.getElementById('barcode').value;

            // send the barcode to the server using fetch
            fetch('process_barcode.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        barcode: barcode,
                        action: 'view'
                    })
                })
                .then(response => response.text())
                .then(data => {
                    const infoDiv = document.getElementById('item-info');
                    infoDiv.style.display = 'block'; // show the info container
                    infoDiv.innerHTML = data; // update the container with the response
                })
                .catch(error => {
                    console.error('Error:', error);
                    const infoDiv = document.getElementById('item-info');
                    infoDiv.style.display = 'block'; // show the info container
                    infoDiv.innerHTML = `<p class="error">An error occurred while fetching item information. Please try again.</p>`;
                });
        });
    </script>
</body>

</html>