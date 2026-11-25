
<?php

class Department extends Model
{
    // Display all departments
    public function getAllDepartments()
    {
        $sql = "SELECT * FROM departments
                ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    // Find department by ID
    public function getDepartmentById($id)
    {
        $sql = "SELECT * FROM departments
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    // Add new department
    public function addDepartment($name, $description)
    {
        $sql = "INSERT INTO departments
                (department_name, description)
                VALUES
                (:name, :description)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':description' => $description
        ]);
    }


    // Update department
    public function updateDepartment(
        $id,
        $name,
        $description,
        $status
    ) {
        $sql = "UPDATE departments
                SET department_name = :name,
                    description = :description,
                    status = :status
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':status' => $status,
            ':id' => $id
        ]);
    }


    // Delete department
    public function deleteDepartment($id)
    {
        $sql = "DELETE FROM departments
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

}
