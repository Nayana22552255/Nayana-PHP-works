<?php
class Doctor 
{
    private $conn;
    private $table="doctors";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllDoctors()
    {
        $sql="SELECT d.*,dep.department_name
                FROM doctors d
                LEFT JOIN departments dep
                ON d.department_id=dep.department_id
                ORDER BY d.doctor_id DESC";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDoctorById($id)
    {
        $sql="SELECT d.*,dep.department_name
                FROM doctors d 
                LEFT JOIN departments dep
                ON d.department_id=dep.department_id
                WHERE d.doctor_id=?";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function addDoctors($data)
    {
        $sql="INSERT INTO doctors
                (doctor_name, specialization,phone,email,department_id)
                VALUES(?, ?, ?, ?, ?)";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([
            $data['doctor_name'],
            $data['specialization'],
            $data['phone'],
            $data['email'],
            $data['department_id']
        ]);

    }


    public function updateDoctor($id,$data)
    {
        $sql="UPDATE doctors
                SET doctor_name=?,
                specialization=?,
                phone=?,
                email=?,
                department_id=?
                WHERE doctor_id=?";
        $stmt=$this->conn->prepare($sql);

        return $stmt->execute([
                    $data['doctor_name'],
                    $data['specialization'],
                    $data['phone'],
                    $data['email'],
                    $data['department_id'],
                    $id
        ]);
    }

    public function deleteDoctor($id)
    {
        $sql="DELETE FROM doctors WHERE doctor_id=?";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function searchDoctors($keyword)
    {
        $sql="SELECT d.*,department_name
                FROM doctors d
                LEFT JOIN departments dep
                ON d.department_id=dep.department_id
                WHERE d.doctor_name LIKE ?
                OR d.specialization LIKE ?
                OR dep.department_name LIKE ?";
        $stmt=$this->conn->prepare($sql);
        $search="%". $keyword ."%";
        $stmt->execute([$search,$search,$search]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDepartments()
    {
        $defaultDepartments = [
            'Cardiology',
            'Dermatology',
            'Emergency Medicine',
            'Endocrinology',
            'General Medicine',
            'Gynecology',
            'Neurology',
            'Oncology',
            'Ophthalmology',
            'Orthopedics',
            'Pediatrics',
            'Radiology'
        ];
        $insertDepartment = $this->conn->prepare(
            "INSERT INTO departments (department_name)
             SELECT ? WHERE NOT EXISTS (
                 SELECT 1 FROM departments WHERE department_name = ?
             )"
        );

        foreach ($defaultDepartments as $departmentName) {
            $insertDepartment->execute([$departmentName, $departmentName]);
        }

        $sql="SELECT * FROM departments
        ORDER BY department_name";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>