<?php
session_start(); // start session to access scanned items

if (isset($_POST['item_index'])) {
    $index = intval($_POST['item_index']); // get the index of the item to remove

    // retrieve the item details from the session
    $item = $_SESSION['scanned_items'][$index];

    // restore the item's quantity in the database
    $dsn = 'mysql:host=localhost;dbname=HIMAROS_DB';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // update query to increment the quantity of the removed item
        $stmt = $pdo->prepare("
            UPDATE CURRENT_STOCK
            SET Quantity = Quantity + 1
            WHERE Barcode = :barcode
        ");
        $stmt->bindParam(':barcode', $item['Barcode']); // use the item's barcode
        $stmt->execute();
    } catch (PDOException $e) {
        // handle database errors
        echo "<h2>Database Error: " . $e->getMessage() . "</h2>";
        exit();
    }

    // remove the item from the session
    unset($_SESSION['scanned_items'][$index]);
    $_SESSION['scanned_items'] = array_values($_SESSION['scanned_items']); // reindex the array
}

// redirect back to the use_item page
header('Location: use_item.php');
exit();
?>
