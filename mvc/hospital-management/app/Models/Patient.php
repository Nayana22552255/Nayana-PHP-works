<?php

class Patient 
{
    /** @var PDO */
    private $conn;
    private $table = "patients";

    public function __construct()
    {
        $database=new Database();
        $this->conn= $database->connect();
    }

    //get all pateints
    public function getAllPatients()
    {

        $sql="SELECT * FROM ". $this->table;
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    /**
     * @param array{patient_name: string, gender: string, date_of_birth: string, phone: string, email: string, address: string} $data
     */
    public function addPatients($data)
    {
        $sql="INSERT INTO patients
              (patient_name, gender, date_of_birth, phone, email, address)
              VALUES(?, ?, ?, ?, ?, ?)";
        $stmt=$this->conn->prepare($sql);

        return $stmt->execute([
           $data['patient_name'],
           $data['gender'],
           $data['date_of_birth'],
           $data['phone'],
           $data['email'],
           $data['address'] 
        ]);
    }
}
?>