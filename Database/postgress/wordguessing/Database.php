
<?php

class Database
{
    private $host = "localhost";
    private $dbname = "word_db";
    private $user = "postgres";
    private $password = "root";

    public function connect()
    {
        try {
            $conn = new PDO(
                "pgsql:host={$this->host};port=5432;dbname={$this->dbname}",
                $this->user,
                $this->password
            );

            $conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $conn;

        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }
}
