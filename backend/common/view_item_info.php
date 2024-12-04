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
    <style>
        /* general body styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        /* header styling */
        h1 {
            background-color: #216491;
            color: white;
            padding: 20px;
            margin: 0;
            font-size: 24px;
            text-align: center;
        }

        /* form container styling */
        .scan-box {
            background-color: #216491;
            color: white;
            border-radius: 8px;
            padding: 40px;
            margin: 20px auto;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 90%;
            max-width: 600px;
            flex-direction: column;
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
            color: white;
        }

        .scan-section input {
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
            max-width: 400px;
        }

        .scan-section button {
            background-color: #216491;
            color: white;
            padding: 15px 50px;
            border: 2px solid white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }

        .scan-section button:hover {
            background-color: #1a4f6e;
            border-color: #ffc107;
        }

        #item-info {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #d9d9d9;
            border-radius: 8px;
            background-color: #ffffff;
            color: #333;
            display: none; /* hidden by default */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            text-align: left;
        }

        .error {
            color: #e74c3c;
            font-weight: bold;
        }

       /* Back Button */
       .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #1a4f6e;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-button:hover {
            background-color: #155bb5;
        }
    </style>
</head>
<body>
    <h1>View Item Info</h1>

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

    <!-- back button -->
    <button class="back-button" onclick="location.href='<?php echo $back_link; ?>'">Back</button>

    <script>
        document.getElementById('submit-button').addEventListener('click', function(e) {
            const barcode = document.getElementById('barcode').value;

            // send the barcode to the server using fetch
            fetch('process_barcode.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ barcode: barcode, action: 'view' })
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
