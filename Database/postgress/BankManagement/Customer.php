<?php
class Customer
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }


    public function addCustomer(string $name,string $email,string $phone,string $address): bool
    {
        $sql="INSERT INTO customers(customer_name,email,phone,address)
        VALUES(:name,:email,:phone,:address)";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([
            ':name'=>$name,
            ':email'=>$email,
            ':phone'=>$phone,
            ':address'=>$address
        ]);
    }


    public function getCustomers(): array
    {
        $sql="SELECT * FROM customers ORDER BY customer_id";
        $stmt=$this->conn->query($sql);
        return $stmt->fetchALL(PDO::FETCH_ASSOC);
    }

    public function getCustomerById(int $customer_id): array
    {
        $sql="SELECT * FROM customers WHERE customer_id=:customer_id";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([':customer_id'=>$customer_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function  searchCustomers(string $keyword):array
    {
        $sql="SELECT *FROM customers WHERE customer_name LIKE :keyword OR email LIKE :keyword";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([':keyword'=>"%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCustomer(int $customer_id,string $name,string $email,string $phone,string $address): bool
    {
        $sql="UPDATE customers SET customer_name=:name,email=:email,phone=:phone,address=:address WHERE customer_id=:customer_id";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([
            ':customer_id'=>$customer_id,
            ':name'=>$name,
            ':email'=>$email,
            ':phone'=>$phone,
            ':address'=>$address
        ]);
    }

    public function deleteCustomer(int $customer_id):bool
    {
        $sql="DELETE FROM customers WHERE customer_id=:customer_id";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([':customer_id'=>$customer_id]);
    }
}