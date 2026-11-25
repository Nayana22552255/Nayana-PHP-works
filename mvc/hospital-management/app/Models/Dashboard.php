<?php

class Dashboard
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getSummary()
    {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM patients) AS total_patients,
                    (SELECT COUNT(*) FROM doctors) AS total_doctors,
                    (SELECT COUNT(*) FROM appointments) AS total_appointments,
                    (SELECT COUNT(*)
                     FROM appointments
                     WHERE appointment_date = CURDATE()) AS appointments_today,
                    (SELECT COUNT(*) FROM prescriptions) AS total_prescriptions,
                    (SELECT COUNT(*) FROM bills) AS total_bills,
                    (SELECT COALESCE(SUM(total_amount), 0)
                     FROM bills) AS total_billed,
                    (SELECT COALESCE(SUM(amount), 0)
                     FROM payments) AS total_paid";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUpcomingAppointments()
    {
        $sql = "SELECT
                    a.appointment_id,
                    a.appointment_date,
                    a.appointment_time,
                    a.status,
                    p.patient_name,
                    d.doctor_name
                FROM appointments a
                JOIN patients p
                    ON a.patient_id = p.patient_id
                JOIN doctors d
                    ON a.doctor_id = d.doctor_id
                WHERE (
                    a.appointment_date > CURDATE()
                    OR (
                        a.appointment_date = CURDATE()
                        AND a.appointment_time >= CURTIME()
                    )
                )
                  AND a.status <> 'Cancelled'
                ORDER BY a.appointment_date ASC,
                         a.appointment_time ASC
                LIMIT 5";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}