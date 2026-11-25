
<?php

class Appointment
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get all appointments
    public function getAllAppointments()
    {
        $sql = "SELECT
                    a.*,
                    p.patient_name,
                    d.doctor_name
                FROM appointments a
                JOIN patients p
                    ON a.patient_id = p.patient_id
                JOIN doctors d
                    ON a.doctor_id = d.doctor_id
                ORDER BY a.appointment_date DESC,
                         a.appointment_time DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get one appointment
    public function getAppointmentById($id)
    {
        $sql = "SELECT * FROM appointments
                WHERE appointment_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add appointment
    public function addAppointment($data)
    {
        $sql = "INSERT INTO appointments
                (patient_id, doctor_id, appointment_date,
                 appointment_time, reason, status)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $data['patient_id'],
            $data['doctor_id'],
            $data['appointment_date'],
            $data['appointment_time'],
            $data['reason'],
            $data['status']
        ]);
    }

    // Update appointment
    public function updateAppointment($id, $data)
    {
        $sql = "UPDATE appointments
                SET patient_id = ?,
                    doctor_id = ?,
                    appointment_date = ?,
                    appointment_time = ?,
                    reason = ?,
                    status = ?
                WHERE appointment_id = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $data['patient_id'],
            $data['doctor_id'],
            $data['appointment_date'],
            $data['appointment_time'],
            $data['reason'],
            $data['status'],
            $id
        ]);
    }

    // Delete appointment
    public function deleteAppointment($id)
    {
        $sql = "DELETE FROM appointments
                WHERE appointment_id = ?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Get patients for dropdown
    public function getPatients()
    {
        $sql = "SELECT patient_id, patient_name
                FROM patients
                ORDER BY patient_name";

        return $this->conn->query($sql)
                          ->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get doctors for dropdown
    public function getDoctors()
    {
        $sql = "SELECT doctor_id, doctor_name
                FROM doctors
                ORDER BY doctor_name";

        return $this->conn->query($sql)
                          ->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search appointments
    public function searchAppointments($keyword)
    {
        $sql = "SELECT
                    a.*,
                    p.patient_name,
                    d.doctor_name
                FROM appointments a
                JOIN patients p
                    ON a.patient_id = p.patient_id
                JOIN doctors d
                    ON a.doctor_id = d.doctor_id
                WHERE p.patient_name LIKE ?
                   OR d.doctor_name LIKE ?
                ORDER BY a.appointment_date DESC";

        $stmt = $this->conn->prepare($sql);
        $search = "%" . $keyword . "%";
        $stmt->execute([$search, $search]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
