<?php
class Database {
    private static $connection;

    // Method to get a single instance of the database connection
    public static function getConnection() {
        if (!self::$connection) {
            $servername = "127.0.0.1";
            $username = "root"; // Your phpMyAdmin user
            $password = ""; // Your phpMyAdmin password
            $dbname = "HIMAROS_DB"; // Your database name

            // Create connection
            self::$connection = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if (self::$connection->connect_error) {
                die("Connection failed: " . self::$connection->connect_error);
            }
        }
        return self::$connection;
    }
}
?>
