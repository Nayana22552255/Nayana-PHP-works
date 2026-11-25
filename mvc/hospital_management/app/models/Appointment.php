
<?php

class Appointment extends Model
{
    // Get all appointments
    public function getAllAppointments($keyword = '')
    {
        $sql = "SELECT
                a.*,
                p.patient_number,
                p.first_name AS patient_first_name,
                p.last_name AS patient_last_name,
                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name,
                dep.department_name

            FROM appointments a

            INNER JOIN patients p
                ON a.patient_id = p.id

            INNER JOIN doctors d
                ON a.doctor_id = d.id

            LEFT JOIN departments dep
                ON d.department_id = dep.id
        ";

        if ($keyword !== '') {
            $sql .= "
                WHERE
                    a.appointment_number LIKE :number
                    OR p.first_name LIKE :patient_first
                    OR p.last_name LIKE :patient_last
                    OR d.first_name LIKE :doctor_first
                    OR d.last_name LIKE :doctor_last
            ";
        }

        $sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

        $stmt = $this->db->prepare($sql);

        if ($keyword !== '') {
            $search = '%' . $keyword . '%';

            $stmt->execute([
                ':number' => $search,
                ':patient_first' => $search,
                ':patient_last' => $search,
                ':doctor_first' => $search,
                ':doctor_last' => $search
            ]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get appointment by ID
    public function getAppointmentById($id)
    {
        $sql = "
            SELECT
                a.*,
                p.patient_number,
                p.first_name AS patient_first_name,
                p.last_name AS patient_last_name,
                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name,
                dep.department_name

            FROM appointments a

            INNER JOIN patients p
                ON a.patient_id = p.id

            INNER JOIN doctors d
                ON a.doctor_id = d.id

            LEFT JOIN departments dep
                ON d.department_id = dep.id

            WHERE a.id = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Get active patients for appointment form
    public function getActivePatients()
    {
        $sql = "
            SELECT
                id,
                patient_number,
                first_name,
                last_name,
                phone

            FROM patients

            WHERE status = 1

            ORDER BY first_name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Get active doctors for appointment form
    // Get active doctors for appointment form
    public function getActiveDoctors()
    {
        $sql = "
            SELECT
                d.id,
                d.first_name,
                d.last_name,
                d.specialization,
                dep.department_name

            FROM doctors d

            INNER JOIN users u
                ON d.user_id = u.id

            INNER JOIN departments dep
                ON d.department_id = dep.id

            WHERE d.status = 1
            AND u.status = 1
            AND dep.status = 1

            ORDER BY d.first_name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Check doctor appointment time conflict
    public function hasDoctorConflict(
        $doctorId,
        $date,
        $time,
        $duration,
        $excludeId = null
    ) {
        $startSeconds = strtotime($time) - strtotime('00:00:00');

        $endSeconds = $startSeconds + ($duration * 60);

        $sql = "
            SELECT COUNT(*)

            FROM appointments

            WHERE doctor_id = :doctor_id

            AND appointment_date = :appointment_date

            AND status IN ('Scheduled', 'Confirmed')

            AND TIME_TO_SEC(appointment_time) < :new_end

            AND (
                TIME_TO_SEC(appointment_time)
                + (duration_minutes * 60)
            ) > :new_start
        ";

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        $stmt = $this->db->prepare($sql);

        $params = [
            ':doctor_id' => $doctorId,
            ':appointment_date' => $date,
            ':new_end' => $endSeconds,
            ':new_start' => $startSeconds
        ];

        if ($excludeId !== null) {
            $params[':exclude_id'] = $excludeId;
        }

        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }


    // Create new appointment
    public function createAppointment($data)
    {
        $this->db->beginTransaction();

        try {

            // Check doctor availability
            if ($this->hasDoctorConflict(
                $data['doctor_id'],
                $data['appointment_date'],
                $data['appointment_time'],
                $data['duration_minutes']
            )) {
                throw new Exception(
                    'The doctor already has an appointment during this time.'
                );
            }

            // Temporary unique appointment number
            $temporaryNumber = 'TEMP-' . bin2hex(random_bytes(8));

            $sql = "
                INSERT INTO appointments (
                    appointment_number,
                    patient_id,
                    doctor_id,
                    appointment_date,
                    appointment_time,
                    duration_minutes,
                    reason,
                    status,
                    notes,
                    created_by
                )

                VALUES (
                    :appointment_number,
                    :patient_id,
                    :doctor_id,
                    :appointment_date,
                    :appointment_time,
                    :duration_minutes,
                    :reason,
                    :status,
                    :notes,
                    :created_by
                )
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':appointment_number' => $temporaryNumber,
                ':patient_id' => $data['patient_id'],
                ':doctor_id' => $data['doctor_id'],
                ':appointment_date' => $data['appointment_date'],
                ':appointment_time' => $data['appointment_time'],
                ':duration_minutes' => $data['duration_minutes'],
                ':reason' => $data['reason'] ?? null,
                ':status' => 'Scheduled',
                ':notes' => $data['notes'] ?? null,
                ':created_by' => $data['created_by'] ?? null
            ]);

            // Get newly created appointment ID
            $id = $this->db->lastInsertId();

            // Generate final appointment number
            $appointmentNumber = 'APT'
                . date('Y')
                . str_pad($id, 6, '0', STR_PAD_LEFT);

            $update = $this->db->prepare("
                UPDATE appointments

                SET appointment_number = :number

                WHERE id = :id
            ");

            $update->execute([
                ':number' => $appointmentNumber,
                ':id' => $id
            ]);

            $this->db->commit();

            return $id;

        } catch (Exception $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }


    // Update appointment details
    public function updateAppointment($id, $data)
    {
        $this->db->beginTransaction();

        try {

            // Check doctor availability
            if ($this->hasDoctorConflict(
                $data['doctor_id'],
                $data['appointment_date'],
                $data['appointment_time'],
                $data['duration_minutes'],
                $id
            )) {
                throw new Exception(
                    'The doctor already has an appointment during this time.'
                );
            }

            $sql = "
                UPDATE appointments

                SET
                    patient_id = :patient_id,
                    doctor_id = :doctor_id,
                    appointment_date = :appointment_date,
                    appointment_time = :appointment_time,
                    duration_minutes = :duration_minutes,
                    reason = :reason,
                    notes = :notes

                WHERE id = :id

                AND status IN ('Scheduled', 'Confirmed')
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':patient_id' => $data['patient_id'],
                ':doctor_id' => $data['doctor_id'],
                ':appointment_date' => $data['appointment_date'],
                ':appointment_time' => $data['appointment_time'],
                ':duration_minutes' => $data['duration_minutes'],
                ':reason' => $data['reason'] ?? null,
                ':notes' => $data['notes'] ?? null,
                ':id' => $id
            ]);

            if ($stmt->rowCount() === 0) {

                // Check whether the appointment exists and is editable
                $check = $this->db->prepare("
                    SELECT id
                    FROM appointments
                    WHERE id = :id
                    AND status IN ('Scheduled', 'Confirmed')
                ");

                $check->execute([':id' => $id]);

                if (!$check->fetch()) {
                    throw new Exception(
                        'Appointment not found or cannot be edited.'
                    );
                }
            }

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }


    // Change appointment status
    public function updateStatus($id, $status)
    {
        $allowedStatuses = [
            'Scheduled',
            'Confirmed',
            'Completed',
            'Cancelled',
            'No-show'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new Exception('Invalid appointment status.');
        }

        $sql = "
            UPDATE appointments

            SET status = :status

            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }


// Get doctor ID using logged-in user's ID
public function getDoctorIdByUserId($userId)
{
    $sql = "
        SELECT id
        FROM doctors
        WHERE user_id = :user_id
        AND status = 1
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':user_id' => $userId
    ]);

    return $stmt->fetchColumn();
}


// Mark an appointment as completed by its assigned doctor
public function markDoctorAppointmentCompleted($appointmentId, $doctorId)
{
    $sql = "
        UPDATE appointments
        SET status = 'Completed'
        WHERE id = :appointment_id
        AND doctor_id = :doctor_id
        AND status IN ('Scheduled', 'Confirmed')
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':appointment_id' => $appointmentId,
        ':doctor_id' => $doctorId
    ]);

    return $stmt->rowCount() > 0;
}



// Get appointments assigned to a particular doctor
public function getDoctorAppointments($doctorId)
{
    $sql = "
        SELECT
            a.id,
            a.appointment_number,
            a.appointment_date,
            a.appointment_time,
            a.duration_minutes,
            a.reason,
            a.status,

            p.patient_number,
            p.first_name AS patient_first_name,
            p.last_name AS patient_last_name,
            p.phone AS patient_phone,

            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name

        FROM appointments a

        INNER JOIN patients p
            ON a.patient_id = p.id

        INNER JOIN doctors d
            ON a.doctor_id = d.id

        WHERE a.doctor_id = :doctor_id

        ORDER BY
            a.appointment_date ASC,
            a.appointment_time ASC
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':doctor_id' => $doctorId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Get patient ID using logged-in user's ID
public function getPatientIdByUserId($userId)
{
    $sql = "SELECT id
            FROM patients
            WHERE user_id = :user_id
              AND status = 1
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':user_id' => $userId
    ]);

    return $stmt->fetchColumn();
}


// Get one active doctor for patient booking
public function getActiveDoctorById($doctorId)
{
    $sql = "SELECT
                d.id,
                d.first_name,
                d.last_name,
                d.specialization,
                d.qualification,
                dep.department_name
            FROM doctors d
            INNER JOIN users u
                ON d.user_id = u.id
            INNER JOIN departments dep
                ON d.department_id = dep.id
            WHERE d.id = :doctor_id
              AND d.status = 1
              AND u.status = 1
              AND dep.status = 1
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':doctor_id' => $doctorId
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// Get all appointments belonging to one patient
public function getPatientAppointments($patientId)
{
    $sql = "
        SELECT
            a.id,
            a.appointment_number,
            a.appointment_date,
            a.appointment_time,
            a.duration_minutes,
            a.reason,
            a.status,
            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name,
            d.specialization,
            dep.department_name
        FROM appointments a
        INNER JOIN doctors d
            ON a.doctor_id = d.id
        INNER JOIN departments dep
            ON d.department_id = dep.id
        WHERE a.patient_id = :patient_id
        ORDER BY
            a.appointment_date DESC,
            a.appointment_time DESC
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':patient_id' => $patientId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}
