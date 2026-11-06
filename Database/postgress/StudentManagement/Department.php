
<?php

class Department
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

   
    public function addDepartment(string $name, string $code): bool 
    {

        $sql = "INSERT INTO departments(department_name, department_code)
                VALUES (?, ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([ $name, $code]);
    }

   
    public function getDepartments(): array
    {
        $sql = "SELECT * FROM departments ORDER BY department_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  
    public function getDepartmentById(int $id): ?array
    {
        $sql = "SELECT * FROM departments WHERE department_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        $department = $stmt->fetch(PDO::FETCH_ASSOC);

        return $department === false ? null : $department;
    }

   
    public function updateDepartment(int $id,string $name,string $code): bool
    {

        $sql = "UPDATE departments SET department_name = ?,
                department_code = ? WHERE department_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$name,$code,$id]);

        return $stmt->rowCount() > 0;
    }

    public function deleteDepartment(int $id): bool
    {
        $sql = "DELETE FROM departments WHERE department_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    
    public function searchDepartments(string $keyword): array
    {
        $sql = "SELECT * FROM departments WHERE department_name ILIKE ? ORDER BY department_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["%$keyword%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  
    public function getDepartmentByCode(string $code): ?array
    {
        $sql = "SELECT * FROM departments WHERE department_code = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$code]);

        $department = $stmt->fetch(PDO::FETCH_ASSOC);

        return $department === false ? null : $department;
    }


    public function getTotalDepartments(): int
    {
        $sql = "SELECT COUNT(*) FROM departments";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}
