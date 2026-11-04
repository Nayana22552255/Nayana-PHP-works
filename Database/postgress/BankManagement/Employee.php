<?php 
class Employee
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function addEmployees(string $name,string $email,string $role):bool 
    {
        $sql="INSERT INTO employees(employee_name,email,role)VALUES(:name, :email, :role)";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([
            ':name'=>$name,
            ':email'=>$email,
            ':role'=>$role
        ]);
    }

    public  function getAllEmployees():array
    {
        $sql="SELECT *FROM employees ORDER BY employee_id";
        $stmt=$this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeeById(int $id):array 
    {
        $sql="SELECT *FROM employees WHERE employee_id=:id";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateEmployee(int $id,string $name,string $email,string $role):bool 
    {
        $sql="UPDATE employees SET employee_name=:name,
                    email=:email,
                    role=:role 
                    WHERE employee_id=:id";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([':name'=>$name,
                        ':email'=>$email,
                        ':role'=>$role,
                        ':id'=>$id]);
        return $stmt->rowCount() > 0;
    }

    public function deleteEmployee(int $id):bool 
    {
        $sql="DELETE FROM employees WHERE employee_id=:id";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([':id'=>$id]);
        return $stmt->rowCount() > 0;
    }
}