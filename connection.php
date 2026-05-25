<?php
class Database {

    public static $connection;

    public static function setUpConnection() {
        if (!isset(self::$connection)) {
            // Enable exceptions for mysqli
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
            try {
                self::$connection = new mysqli(
                    "localhost",
                    "root",
                    "root",
                    "uniconnect",
                    3306
                );
                
                // Set charset to avoid encoding issues
                self::$connection->set_charset("utf8mb4");
                
            } catch (Exception $e) {
                // Throw a clean exception instead of die()
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
    }

    public static function iud($q) {
        self::setUpConnection();
        
        try {
            $result = self::$connection->query($q);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }

    public static function search($q) {
        self::setUpConnection();
        
        try {
            $result = self::$connection->query($q);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Search query failed: " . $e->getMessage());
        }
    }
}
?>