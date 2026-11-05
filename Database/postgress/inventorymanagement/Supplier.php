<?php
class Supplier 
{
    private PDO $conn;
    public function __construct(PDO $conn)
    {
            $this->conn=$conn;
    }

    public function addSuppliers(string $name,string $phone,string $email, string $address): bool 
    {
        $sql="INSERT INTO suppliers(supplier_name,phone,email,address)
                VALUES(?,?,?,?)";

        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$name,$phone,$email,$address]);
    }

    public function displaySuppliers()
    {
        $sql= "SELECT * FROM suppliers ORDER BY supplier_id";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePhone(int $id, string $phone): bool
    {
        $sql="UPDATE suppliers SET phone=? WHERE supplier_id=?";

        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$phone,$id]);
    }

}