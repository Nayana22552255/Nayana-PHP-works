<?php

class Prescription
{
    private $conn;
    public function __construct($db)
    {
        $this->conn=$db;
    }

    public function getAllPrescriptions()
    {
        $sql="SELECT p.prescription_id,
                    p.appointment_id,
                    p.notes,
                    p.created_at,
                    pt.patient_name,
                    d.doctor_name,
                    a.appointment_date
                    FROM prescriptions p
                    JOIN appointments a
                    ON p.appointment_id=a.appointment_id
                    JOIN patients pt
                    ON a.patient_id=pt.patient_id
                    JOIN doctors d
                    ON a.doctor_id=d.doctor_id
                    ORDER BY p.created_at DESC";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPrescriptionById($id)
    {
        $sql="SELECT  p,*,
                      a.appointment_date,
                      a.appointment_time,
                      pt.patient_name,
                      d.doctor_name
                      FROM prescriptions p
                      JOIN appointments a
                      ON p.appointment_id=a.appointment_id
                      JOIN patients pt
                      ON a.patient_id=pt.patient_id
                      JOIN doctors d
                      ON a.doctor_id=d.doctor_id
                      WHERE p.prescription_id=?";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPrescriptionItems($prescriptionId)
    {
        $sql="SELECT *
              FROM prescription_items
              WHERE prescription_id=?
              ORDER  BY item_id";

        $stmt=$this->conn->prepae($sql);
        $stmt->execute($sql);

        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAppointments()
    {
        $sql="SELECT
                    a.appointment_id,
                    a.appointment_date,
                    a.appointment_time,
                    pt.patient_name,
                    d.doctor_name
                    FROM appointments a
                    JOIN patients pt
                    ON a.patient_id=pt.patient_id,
                    JOIN doctors d
                    ON a.doctor_id=d.doctor_id
                    WHERE a.status='Completed'
                    ORDER BY a.appointment_date DESC";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addPrescription($data,$items)
    {
        try 
        {
            $this->conn->beginTransaction();

            $sql="INSERT INTO precsriptions(appointment_id,notes)VALUES(?,?)";
            $stmt=$this->conn->prepare($sql);
            $stmt->execute([
                $data['appointment_id'],
                $data['notes']
            ]);
            $prescriptionId=$this->conn->lastInsertId();

            $itemSql="INSERT INTO prescription_items
                        (prescription_id,medicine_name,dosage,frequency,duration,instructions)
                        VALUES(?, ?, ?, ?, ?, ?)";
            $itemStmt=$this->conn->prepare($itemSql);
            foreach($items as $item)
                {
                    $itemStmt->execute([
                        $prescriptionId,
                        $item['medicine_name'],
                        $item['dosage'],
                        $item['frequency'],
                        $item['duration'],
                        $item['instructions']
                    ]);
                }

            $this->conn->commit();
            return $prescriptionId;
        }catch(PDOException $e)
        {
            if($this->conn->inTransaction()){
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    public function updatePrescription($id,$data)
    {
        $sql="UPDATE prescriptions
                SET appointment_id=?,
                notes=?
                WHERE prescription_id=?";
        $stmt=$this->conn->prepare($sql);

        return $stmt->execute([
            $data['appointment_id'],
            $data['notes'],
            $id
        ]);
    }

    public function updatePrescriptionItems($prescriptionId,$items)
    {
        try {
            $this->conn->beginTransaction();
            $deleteSql="DELETE FROM prescription_items
                        WHERE prescription_id=?";
            $delteStmt=$this->conn->prepare($deleteSql);
            $delteStmt->execute([$prescriptionId]);

            $insertSql="INSERT INTO prescription_items
                            (prescription_id,medicine_name,dosage,frequency,duration,instructions)
                            VALUES(?, ?, ?, ?, ?, ?, ?)";
            $insertStmt=$this->conn->prepare($insertSql);
            foreach($items as $item)
                {
                    $insertStmt->execute([
                    $prescriptionId,
                    $item['medicine_name'],
                    $item['dosage'],
                    $item['frequency'],
                    $item['duration'],
                    $item['instructions']
                    ]);
                }
            $this->conn->commit();
            return true;
        }catch(PDOException $e){
            if($this->conn->intransaction()){
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    public function deletePresription($id)
    {
        $sql="DELETE FROM prescriptions 
                WHERE prescription_id=?";

        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function searchPrescriptions($keyword)
    {
        $sql = "SELECT
                    p.*,
                    pt.patient_name,
                    d.doctor_name,
                    a.appointment_date
                FROM prescriptions p
                JOIN appointments a
                    ON p.appointment_id = a.appointment_id
                JOIN patients pt
                    ON a.patient_id = pt.patient_id
                JOIN doctors d
                    ON a.doctor_id = d.doctor_id
                WHERE pt.patient_name LIKE ?
                   OR d.doctor_name LIKE ?
                ORDER BY p.created_at DESC";

        $search = "%" . $keyword . "%";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$search, $search]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>