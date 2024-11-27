<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HIMAROS_DB";  // Make sure this matches the name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // echo "Connected successfully"; // Uncomment this line if you want to confirm the connection
}

?>
