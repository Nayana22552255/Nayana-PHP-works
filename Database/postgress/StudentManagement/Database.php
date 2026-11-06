<?php
class Database 
{
    private string $host="localhost";
    private string $dbname="student_db";
    private string $username="postgres";
    private string $password="root";

    public function connect():PDO 
    {
        try 
        {
            $conn=new PDO(
                "pgsql:host={$this->host};port=5432;dbname={$this->dbname}",
                $this->username,
                $this->password
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            return $conn;
        }catch(PDOException $e)
        {
            die("Dtabase connection failed: ". $e->getMessage(). "\n");
        }
    }
}