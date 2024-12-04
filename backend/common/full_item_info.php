<?php
session_start();
include(__DIR__ . '/../../config/database.php');
include('../assets/inc/checklogin.php');
checklogin('doctor'); // Role = 'doctor'

$mysqli = Database::getConnection();

// Check if `item` parameter is set
if (!isset($_GET['item'])) {
    die("No item specified!");
}

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

// Fetch the item name from the GET request
$item_name = $_GET['item'];

// Prepare the SQL query to fetch the item's full information
$stmt = $mysqli->prepare("
    SELECT 
        i.Item_Name, 
        i.Category, 
        i.Picture_URL, 
        i.Cost, 
        i.Item_Type, 
        i.Item_Description, 
        i.Min_Quantity, 
        i.Being_Used, 
        s.Suppleir_Name, 
        s.Contact_Info, 
        s.Email, 
        s.Supplier_Address 
    FROM ITEM i
    JOIN SUPPLIER s ON i.SupplierID = s.SupplierID
    WHERE i.Item_Name = ?
");
$stmt->bind_param("s", $item_name);
$stmt->execute();
$result = $stmt->get_result();

// Check if the item exists
if ($result->num_rows === 0) {
    die("Item not found!");
}

$item = $result->fetch_assoc();
$stmt->close();

// Fetch the total quantity of the item excluding expired stock
$stmt = $mysqli->prepare("
    SELECT SUM(cs.Quantity) AS Total_Quantity
    FROM CURRENT_STOCK cs
    JOIN ITEM i ON cs.ItemID = i.ItemID
    WHERE i.Item_Name = ? AND cs.Expiration_Date >= CURDATE()
");
$stmt->bind_param("s", $item_name);
$stmt->execute();
$result = $stmt->get_result();
$total_quantity = $result->fetch_assoc()['Total_Quantity'] ?? 0;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Item Info</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #1a4f6e;
        }
        .item-details img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .item-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .item-details .info {
            flex: 1;
        }
        .info table {
            width: 100%;
            border-collapse: collapse;
        }
        .info table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .info table td:first-child {
            font-weight: bold;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #1a4f6e;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            background: #216491;
        }
    </style>
</head>

<body>
<body>
    <div class="container">
        <h1>Full Information: <?php echo htmlspecialchars($item['Item_Name']); ?></h1>
        <div class="item-details">
            <div class="image">
                <?php if ($item['Picture_URL']): ?>
                    <img src="<?php echo htmlspecialchars($item['Picture_URL']); ?>" alt="Item Image">
                <?php else: ?>
                    <p>No image available.</p>
                <?php endif; ?>
            </div>
            <div class="info">
                <table>
                    <tr>
                        <td>Category</td>
                        <td><?php echo htmlspecialchars($item['Category']); ?></td>
                    </tr>
                    <tr>
                        <td>Cost</td>
                        <td>$<?php echo number_format($item['Cost'], 2); ?></td>
                    </tr>
                    <tr>
                        <td>Item Type</td>
                        <td><?php echo htmlspecialchars($item['Item_Type']); ?></td>
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td><?php echo htmlspecialchars($item['Item_Description']); ?></td>
                    </tr>
                    <tr>
                        <td>Minimum Quantity</td>
                        <td><?php echo htmlspecialchars($item['Min_Quantity']); ?></td>
                    </tr>
                    <tr>
                        <td>Being Used</td>
                        <td><?php echo htmlspecialchars($item['Being_Used']); ?></td>
                    </tr>
                    <tr>
                        <td>Total Quantity in Stock</td>
                        <td><?php echo htmlspecialchars($total_quantity); ?></td>
                    </tr>
                    <tr>
                        <td>Supplier Name</td>
                        <td><?php echo htmlspecialchars($item['Suppleir_Name']); ?></td>
                    </tr>
                    <tr>
                        <td>Supplier Contact</td>
                        <td><?php echo htmlspecialchars($item['Contact_Info']); ?></td>
                    </tr>
                    <tr>
                        <td>Supplier Email</td>
                        <td><?php echo htmlspecialchars($item['Email']); ?></td>
                    </tr>
                    <tr>
                        <td>Supplier Address</td>
                        <td><?php echo htmlspecialchars($item['Supplier_Address']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <button class="back-btn" onclick="location.href='<?php echo $back_link; ?>'">Back to Dashboard</button>
    </div>
</body>

</html>
