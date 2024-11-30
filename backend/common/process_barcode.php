<?php
session_start(); // start session for storing scanned items

// enable error reporting to catch any errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // retrieve barcode and action from the form
    $barcode = htmlspecialchars($_POST['barcode']);
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // check if the barcode is empty
    if (empty($barcode)) {
        echo "<h2>Error: no barcode provided please try again</h2>";
        if ($action === 'view') {
            echo "<button onclick=\"location.href='view_item_info.php'\">Back</button>";
        } else {
            echo "<button onclick=\"location.href='use_item.php'\">Back</button>";
        }
        exit();
    }

    // database connection details
    $dsn = 'mysql:host=localhost;dbname=HIMAROS_DB';
    $username = 'root';
    $password = '';

    try {
        // establish connection to the database
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($action === 'view') {
            // code to handle 'view' action
            // query to fetch item and stock details
            $stmt = $pdo->prepare("
                SELECT 
                    i.Item_Name, 
                    cs.Quantity, 
                    cs.Expiration_Date, 
                    s.Suppleir_Name
                FROM CURRENT_STOCK cs
                JOIN ITEM i ON cs.ItemID = i.ItemID
                JOIN SUPPLIER s ON i.SupplierID = s.SupplierID
                WHERE cs.Barcode = :barcode
            ");
            $stmt->bindParam(':barcode', $barcode);
            $stmt->execute();

            // check if a matching record exists
            if ($stmt->rowCount() > 0) {
                $item = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "<h2>Item Found:</h2>";
                echo "Item Name: " . htmlspecialchars($item['Item_Name']) . "<br>";
                echo "Quantity: " . htmlspecialchars($item['Quantity']) . "<br>";
                echo "Expiration Date: " . htmlspecialchars($item['Expiration_Date']) . "<br>";
                echo "Supplier: " . htmlspecialchars($item['Suppleir_Name']) . "<br>";
            } else {
                echo "<h2>No item found with this barcode.</h2>";
            }

            // add back button to return to view_item_info.php
            echo "<button onclick=\"location.href='view_item_info.php'\">Back</button>";

        } elseif ($action === 'use') {
            // existing code for 'use' action
            // code to decrement quantity of the scanned item
            $stmt = $pdo->prepare("
                UPDATE CURRENT_STOCK
                SET Quantity = Quantity - 1
                WHERE Barcode = :barcode AND Quantity > 0
            ");
            $stmt->bindParam(':barcode', $barcode);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // fetch updated item details
                $stmt = $pdo->prepare("
                    SELECT 
                        i.Item_Name, 
                        cs.Quantity, 
                        cs.Expiration_Date, 
                        s.Suppleir_Name
                    FROM CURRENT_STOCK cs
                    JOIN ITEM i ON cs.ItemID = i.ItemID
                    JOIN SUPPLIER s ON i.SupplierID = s.SupplierID
                    WHERE cs.Barcode = :barcode
                ");
                $stmt->bindParam(':barcode', $barcode);
                $stmt->execute();

                $item = $stmt->fetch(PDO::FETCH_ASSOC);

                // include barcode in session data for removal purposes
                $item['Barcode'] = $barcode;

                // initialize the scanned items list if not already set
                if (!isset($_SESSION['scanned_items'])) {
                    $_SESSION['scanned_items'] = [];
                }

                // check for duplicate scans within 3 seconds
                $lastItem = end($_SESSION['scanned_items']);
                if ($lastItem && $lastItem['Item_Name'] === $item['Item_Name'] && time() - $_SESSION['last_scan_time'] < 3) {
                    echo "<script>
                        if (!confirm('you scanned the same item twice in a short time are you sure')) {
                            window.history.back();
                        }
                    </script>";
                } else {
                    $_SESSION['last_scan_time'] = time(); // record last scan time
                    $_SESSION['scanned_items'][] = $item; // add item to session
                }

                // redirect to use_item page
                header("Location: use_item.php");
                exit();
            } else {
                // display error if item is out of stock or not found
                echo "<h2>Error: this item is out of stock or does not exist</h2>";
                echo "<button onclick=\"location.href='use_item.php'\">Back</button>";
                exit();
            }
        } else {
            // handle invalid action
            echo "<h2>Invalid action specified</h2>";
            echo "<button onclick=\"location.href='barcode.php'\">Back</button>";
            exit();
        }
    } catch (PDOException $e) {
        // handle database errors
        echo "<h2>Database Error: " . $e->getMessage() . "</h2>";
    }
} else {
    // display error for invalid request methods
    echo "<h2>Invalid request method</h2>";
}
?>
