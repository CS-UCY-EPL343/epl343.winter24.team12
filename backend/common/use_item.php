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
    <style>
        /* general body styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            background-color: #216491;
            color: white;
            padding: 20px;
            margin: 0;
            font-size: 24px;
            text-align: center;
        }

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
            background-color: #ffc107;
            color: #1a4f6e;
            padding: 10px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .scan-section button:hover {
            background-color: #ffca28;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
            width: 90%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        li {
            padding: 15px;
            border: 1px solid #d9d9d9;
            border-radius: 4px;
            background-color: white;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .remove-button {
            margin-top: 10px;
            padding: 5px 10px;
            font-size: 14px;
            background-color: #ff5e57;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }

        .remove-button:hover {
            background-color: #e04e47;
        }

        .action-buttons {
            text-align: center;
            margin-top: 20px;
        }

        .action-buttons button {
            margin: 5px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .back-button {
            background-color: #ffc107;
            color: #1a4f6e;
        }

        .back-button:hover {
            background-color: #ffca28;
        }

        .done-button {
            background-color: #ffc107;
            color: #1a4f6e;
        }

        .done-button:hover {
            background-color: #ffca28;
        }
    </style>
</head>

<body>
    <h1>Use Item</h1>

    <!-- scan box -->
    <div class="scan-box">
        <div class="scan-section">
            <form method="POST" action="process_barcode.php">
                <label for="barcode">Enter Barcode:</label>
                <input type="text" id="barcode" name="barcode" placeholder="Scan barcode here..." required>
                <input type="hidden" name="action" value="use">
                <button type="submit">Use Item</button>
            </form>
        </div>
    </div>

    <!-- Scanned Items List -->
    <h2 style="text-align: center;">Scanned Items</h2>
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

    <!-- Action Buttons -->
    <div class="action-buttons">
        <form method="POST" style="display: inline;">
            <button class="back-button" type="button" onclick="location.href='<?php echo $back_link; ?>'">Back</button>
            <button class="done-button" name="clear_session">Done</button>
        </form>
    </div>
</body>

</html>
