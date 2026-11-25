
<?php

class Database
{
    private static $connection = null;

    // Private constructor prevents direct object creation.
    private function __construct()
    {
    }

    public static function connect()
    {
        if (self::$connection === null) {

            $config = require BASE_PATH . '/config/database.php';

            $host = $config['host'];
            $database = $config['database'];
            $username = $config['username'];
            $password = $config['password'];
            $charset = $config['charset'];

            $dsn = "mysql:host=$host;dbname=$database;charset=$charset";

            try {

                self::$connection = new PDO(
                    $dsn,
                    $username,
                    $password
                );

                self::$connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

                self::$connection->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE,
                    PDO::FETCH_ASSOC
                );

                self::$connection->setAttribute(
                    PDO::ATTR_EMULATE_PREPARES,
                    false
                );

            } catch (PDOException $e) {

                error_log($e->getMessage());

                throw new RuntimeException(
                    "Unable to connect to the database."
                );

            }
        }

        return self::$connection;
    }
}
