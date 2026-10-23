<?php 
class Department
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn=$conn;
    }
    public function addDepartment($department_name)
    {
        $sql="INSERT INTO department(department_name) VALUES(?)";

        $stmt=$this->conn->prepare($sql);

        return $stmt->execute([$department_name]);

    }

    public function getDepartment()
    {
        $sql="SELECT *FROM department ORDER BY department_id";

        return $this->conn->query($sql)->fetchAll(PDO:: FETCH_ASSOC);

    } 

    public function updateDepartment($department_id,$department_name)
    {
        $sql="UPDATE department SET department_name= ? WHERE department_id=?";
        $stmt=$this->conn->prepare($sql);

        return $stmt->execute([$department_name,$department_id]);
    }

    public function deleteDepartment($department_id)
    {
        $sql="DELETE FROM  department WHERE department_id=?";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$department_id]);
    }
}
?>