<?php
class Database {

    public static $connection;

    public static function setUpConnection() {
        if (!isset(self::$connection)) {

            self::$connection = new mysqli(
                "localhost",
                "root",
                "root",
                "uniconnectdb",
                3306
            );

            if (self::$connection->connect_error) {
                die(json_encode([
                    "success" => false,
                    "message" => "Connection failed: " . self::$connection->connect_error
                ]));
            }
        }
    }

    public static function iud($q) {
        self::setUpConnection();
        $result = self::$connection->query($q);

        if (!$result) {
            die(json_encode([
                "success" => false,
                "message" => self::$connection->error
            ]));
        }

        return $result;
    }

    public static function search($q) {
        self::setUpConnection();
        $result = self::$connection->query($q);

        if (!$result) {
            die(json_encode([
                "success" => false,
                "message" => self::$connection->error
            ]));
        }

        return $result;
    }
}
?>