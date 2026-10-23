<?php

class Employee
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn=$conn;
    }
    public function addEmployees($name,$email,$department,$salary)
    {
        $sql="INSERT INTO employees(name,email,department,salary)
              values(?,?,?,?)";

        $stmt=$this->conn->prepare($sql);

        return $stmt->execute([
            $name,
            $email,
            $department,
            $salary
        ]);
    }

    public function getEmployees()
    {
        $sql="SELECT * FROM employees ORDER BY id";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    }

    public function updateEmployee($id,$name,$email,$department,$salary)
    {
        $sql="UPDATE employees SET name=?, email=?,department=?,salary=? WHERE id=?";

        $stmt=$this->conn->prepare($sql);

        return $stmt->execute([
            $name,
            $email,
            $department,
            $salary,
            $id,
        
        ]);
    }

    public function deleteEmployees($id)
    {
        $sql="DELETE FROM  employees WHERE id= ?";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
}

?>