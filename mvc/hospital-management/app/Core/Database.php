<?php 

class Database {
    private $host="localhost";
    private $dbname="hospital_db";
    private $username="root";
    private $password ="root";


    public function connect()

    {
        try {
            $conn=new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4",
                $this->username,
                $this->password
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            return $conn;
        }catch (PDOException $e)
        {
            die("Database connection failed: ".$e->getMessage());
        }
    }
}

?>