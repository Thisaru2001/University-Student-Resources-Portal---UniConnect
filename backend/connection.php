<?php
class Database {

    public static $connection;

    /**
     * Load environment variables from .env file
     */
    private static function loadEnv() {
        $envFile = __DIR__ . '/.env';
        
        if (!file_exists($envFile)) {
            throw new Exception('.env file not found');
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse key=value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                // Set to environment
                if (!empty($key)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }

    public static function setUpConnection() {
        if (!isset(self::$connection)) {
            // Load .env file
            self::loadEnv();
            
            // Enable exceptions for mysqli
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            
            try {
                self::$connection = new mysqli(
                    getenv('DB_HOST') ?: 'localhost',
                    getenv('DB_USER') ?: 'root',
                    getenv('DB_PASS') ?: '',
                    getenv('DB_NAME') ?: 'uniconnect',
                    getenv('DB_PORT') ?: 3306
                );
                
                // Set charset
                $charset = getenv('DB_CHARSET') ?: 'utf8mb4';
                self::$connection->set_charset($charset);
                
            } catch (Exception $e) {
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