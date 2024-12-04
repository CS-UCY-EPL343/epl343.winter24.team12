<?php
session_start();
include(__DIR__ . '/../../config/database.php'); 
include('../assets/inc/checklogin.php');
checklogin('doctor'); // Role = 'doctor'

$mysqli = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_new_item'])) {
        $stmt = $mysqli->prepare("CALL AddNewItem(?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $_POST['category'], $_POST['item_name'], $_POST['description'], $_POST['cost'], $_POST['supplier_id']);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['restock_item'])) {
        $stmt = $mysqli->prepare("CALL AddStock(?, ?, ?, ?)");
        $stmt->bind_param('iiss', $_POST['item_id'], $_POST['quantity'], $_POST['expiration_date'], $_POST['barcode']);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Item</title>
</head>
<body>
    <form method="POST">
        <h2>Add New Item</h2>
        <label>Item Name:</label> <input name="item_name" required><br>
        <label>Category:</label> <input name="category" required><br>
        <label>Description:</label> <input name="description" required><br>
        <label>Cost:</label> <input type="number" name="cost" required><br>
        <label>Supplier ID:</label> <input name="supplier_id" required><br>
        <button type="submit" name="add_new_item">Add New Item</button>
    </form>

    <form method="POST">
        <h2>Restock Item</h2>
        <label>Item ID:</label> <input name="item_id" required><br>
        <label>Quantity:</label> <input type="number" name="quantity" required><br>
        <label>Expiration Date:</label> <input type="date" name="expiration_date" required><br>
        <label>Barcode:</label> <input name="barcode" required><br>
        <button type="submit" name="restock_item">Restock Item</button>
    </form>
</body>
</html>
