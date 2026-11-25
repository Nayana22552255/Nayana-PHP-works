<?php

class Database 
{
    private static $connection=null;
    public static function connect()
    {
        if(self::$connection === null)
            {
                $config=require __DIR__. '/../../config/database.php';
                $dsn="pgsql:host={$config['host']};"
                     ."port={$config['port']};"
                     ."dbname={$config['database']}";

                try {
                    self::$connection = new PDO(
                            $dsn,
                            $config['username'],
                            $config['password']
                    );
                    self::$connection->setAttribute(
                            PDO::ATTR_ERRMODE,
                            PDO::ERRMODE_EXCEPTION
                    );
                    self::$connection->setAttribute(
                            PDO::ATTR_DEFAULT_FETCH_MODE,
                            PDO::FETCH_ASSOC
                    );

                    }catch(PDOException $e)
                    {
                        die("Database connection failed: ".$e->getMessage());
                    }
            }
            return self::$connection;
    }
}
?>