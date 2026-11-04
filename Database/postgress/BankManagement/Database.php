<?php
class Database
{
    private string $host="localhost";
    private string $dbname="bank_db";
    private string $username="postgres";
    private string $password="root";

    public PDO $conn;

    public function connect(): PDO
    {
        try
        {
            $this->conn= new PDO(
                "pgsql:host={$this->host};port=5432;dbname={$this->dbname}",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return $this->conn;

        }catch(PDOException  $e)
        {
            die("Database connection failed: ". $e->getMessage());
        }
    }
}

?>