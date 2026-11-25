
<?php

class Prescription extends Model
{
    // 1. Get all prescriptions with patient and doctor information
    public function getAllPrescriptions($keyword = '')
    {
        $sql = "SELECT
                pr.*,
                p.patient_number,
                p.first_name AS patient_first_name,
                p.last_name AS patient_last_name,
                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name
            FROM prescriptions pr

            INNER JOIN patients p
                ON pr.patient_id = p.id

            INNER JOIN doctors d
                ON pr.doctor_id = d.id
        ";

        if (!empty($keyword)) {
            $sql .= "WHERE
                    pr.prescription_number LIKE :prescription_number
                    OR p.patient_number LIKE :patient_number
                    OR p.first_name LIKE :patient_first_name
                    OR p.last_name LIKE :patient_last_name
                    OR d.first_name LIKE :doctor_first_name
                    OR d.last_name LIKE :doctor_last_name
            ";
        }

        $sql .= " ORDER BY pr.id DESC";

        $stmt = $this->db->prepare($sql);

        if (!empty($keyword)) {
            $search = '%' . $keyword . '%';

            $stmt->execute([
                ':prescription_number' => $search,
                ':patient_number' => $search,
                ':patient_first_name' => $search,
                ':patient_last_name' => $search,
                ':doctor_first_name' => $search,
                ':doctor_last_name' => $search
            ]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 2. Get one prescription by ID
    public function getPrescriptionById($id)
    {
        $sql = "SELECT
                pr.*,

                p.patient_number,
                p.first_name AS patient_first_name,
                p.last_name AS patient_last_name,
                p.gender,
                p.date_of_birth,
                p.phone,
                p.email,
                p.address,

                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name,
                d.qualification,
                d.specialization

            FROM prescriptions pr

            INNER JOIN patients p
                ON pr.patient_id = p.id

            INNER JOIN doctors d
                ON pr.doctor_id = d.id

            WHERE pr.id = :id

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // 3. Get all medicines belonging to a prescription
    public function getPrescriptionItems($prescriptionId)
    {
        $sql = "
            SELECT *
            FROM prescription_items
            WHERE prescription_id = :prescription_id
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':prescription_id' => $prescriptionId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 4. Get active patients
    public function getActivePatients()
    {
        $sql = "SELECT
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

    // Get active patients who have appointments with this doctor
public function getPatientsByDoctor($doctorId)
{
    $sql = "SELECT DISTINCT
            p.id,
            p.patient_number,
            p.first_name,
            p.last_name,
            p.phone
        FROM patients p
        INNER JOIN appointments a
            ON a.patient_id = p.id
        WHERE
            a.doctor_id = :doctor_id
            AND p.status = 1
            AND a.status IN (
                'Scheduled',
                'Confirmed',
                'Completed'
            )
        ORDER BY p.first_name ASC
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':doctor_id' => $doctorId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    // 5. Get active doctors
    public function getActiveDoctors()
    {
        $sql = "SELECT
                id,
                first_name,
                last_name,
                qualification,
                specialization
            FROM doctors
            WHERE status = 1
            ORDER BY first_name ASC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // 6. Get appointments belonging to a patient
    public function getAppointmentsByPatient($patientId)
    {
        $sql = " SELECT
                a.id,
                a.appointment_number,
                a.patient_id,
                a.doctor_id,
                a.appointment_date,
                a.appointment_time,
                a.status,

                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name

            FROM appointments a

            INNER JOIN doctors d
                ON a.doctor_id = d.id

            WHERE
                a.patient_id = :patient_id
                AND a.status IN (
                    'Scheduled',
                    'Confirmed',
                    'Completed'
                )

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


    // 7. Create a prescription with multiple medicines
    public function createPrescription($data)
    {
        $this->db->beginTransaction();

        try {

            // Check whether the patient is active
            $patientStmt = $this->db->prepare("SELECT id
                FROM patients
                WHERE id = :id AND status = 1
                LIMIT 1
            ");

            $patientStmt->execute([
                ':id' => $data['patient_id']
            ]);

            if (!$patientStmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception('Patient not found or inactive.');
            }


            // Check whether the doctor is active
            $doctorStmt = $this->db->prepare("SELECT id
                FROM doctors
                WHERE id = :id AND status = 1
                LIMIT 1
            ");

            $doctorStmt->execute([
                ':id' => $data['doctor_id']
            ]);

            if (!$doctorStmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception('Doctor not found or inactive.');
            }


            // Validate appointment relationship if provided
            if (!empty($data['appointment_id'])) {

                $appointmentStmt = $this->db->prepare("SELECT id
                    FROM appointments
                    WHERE
                        id = :appointment_id
                        AND patient_id = :patient_id
                        AND doctor_id = :doctor_id
                        AND status IN (
                            'Scheduled',
                            'Confirmed',
                            'Completed'
                        )
                    LIMIT 1
                ");

                $appointmentStmt->execute([
                    ':appointment_id' => $data['appointment_id'],
                    ':patient_id' => $data['patient_id'],
                    ':doctor_id' => $data['doctor_id']
                ]);

                if (!$appointmentStmt->fetch(PDO::FETCH_ASSOC)) {
                    throw new Exception(
                        'The selected appointment does not match the patient and doctor.'
                    );
                }
            }


            // Check medicine items
            if (
                empty($data['items']) ||
                !is_array($data['items'])
            ) {
                throw new Exception(
                    'At least one medicine is required.'
                );
            }


            // Generate temporary unique prescription number
            $temporaryNumber = 'TEMP-' . bin2hex(random_bytes(12));


            // Insert prescription
            $sql = "INSERT INTO prescriptions (
                    prescription_number,
                    patient_id,
                    doctor_id,
                    appointment_id,
                    prescription_date,
                    diagnosis,
                    notes,
                    status
                ) VALUES (
                    :prescription_number,
                    :patient_id,
                    :doctor_id,
                    :appointment_id,
                    :prescription_date,
                    :diagnosis,
                    :notes,
                    'Active'
                )
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':prescription_number' => $temporaryNumber,
                ':patient_id' => $data['patient_id'],
                ':doctor_id' => $data['doctor_id'],
                ':appointment_id' => !empty($data['appointment_id'])
                    ? $data['appointment_id']
                    : null,
                ':prescription_date' => $data['prescription_date'],
                ':diagnosis' => $data['diagnosis'] ?? null,
                ':notes' => $data['notes'] ?? null
            ]);


            // Get the newly created prescription ID
            $prescriptionId = (int) $this->db->lastInsertId();


            // Generate final prescription number
            $prescriptionNumber = 'PRE' . date('Y') . str_pad(
                $prescriptionId,
                6,
                '0',
                STR_PAD_LEFT
            );


            // Update prescription number
            $updateStmt = $this->db->prepare("UPDATE prescriptions
                SET prescription_number = :number
                WHERE id = :id
            ");

            $updateStmt->execute([
                ':number' => $prescriptionNumber,
                ':id' => $prescriptionId
            ]);


            // Insert medicine items
            $itemSql = "INSERT INTO prescription_items (
                    prescription_id,
                    medicine_name,
                    dosage,
                    frequency,
                    duration,
                    quantity,
                    instructions
                ) VALUES (
                    :prescription_id,
                    :medicine_name,
                    :dosage,
                    :frequency,
                    :duration,
                    :quantity,
                    :instructions
                )
            ";

            $itemStmt = $this->db->prepare($itemSql);


            foreach ($data['items'] as $item) {

                $itemStmt->execute([
                    ':prescription_id' => $prescriptionId,
                    ':medicine_name' => trim($item['medicine_name']),
                    ':dosage' => trim($item['dosage']),
                    ':frequency' => trim($item['frequency']),
                    ':duration' => trim($item['duration']),
                    ':quantity' => (int) $item['quantity'],
                    ':instructions' => $item['instructions'] ?? null
                ]);
            }


            // Save all changes
            $this->db->commit();

            return $prescriptionId;

        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }


    // 8. Update prescription status
    public function updateStatus($id, $status)
    {
        $allowedStatuses = [
            'Active',
            'Completed',
            'Cancelled'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new Exception('Invalid prescription status.');
        }

        $sql = "
            UPDATE prescriptions
            SET status = :status
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }


// 9. Get prescriptions belonging to a specific doctor
public function getPrescriptionsByDoctor($doctorId)
{
    $sql = "SELECT
            pr.*,
            p.patient_number,
            p.first_name AS patient_first_name,
            p.last_name AS patient_last_name,
            p.gender,
            p.phone,
            a.appointment_number

        FROM prescriptions pr

        INNER JOIN patients p
            ON pr.patient_id = p.id

        LEFT JOIN appointments a
            ON pr.appointment_id = a.id

        WHERE pr.doctor_id = :doctor_id

        ORDER BY pr.prescription_date DESC, pr.id DESC
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':doctor_id' => $doctorId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// 10. Get one prescription with details for a specific doctor
public function getPrescriptionDetailsForDoctor($prescriptionId, $doctorId)
{
    $sql = "SELECT
            pr.*,

            p.patient_number,
            p.first_name AS patient_first_name,
            p.last_name AS patient_last_name,
            p.gender,
            p.date_of_birth,
            p.phone,
            p.email,
            p.address,

            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name,
            d.qualification,
            d.specialization,

            a.appointment_number

        FROM prescriptions pr

        INNER JOIN patients p
            ON pr.patient_id = p.id

        INNER JOIN doctors d
            ON pr.doctor_id = d.id

        LEFT JOIN appointments a
            ON pr.appointment_id = a.id

        WHERE
            pr.id = :prescription_id
            AND pr.doctor_id = :doctor_id

        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':prescription_id' => $prescriptionId,
        ':doctor_id' => $doctorId
    ]);

    $prescription = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prescription) {
        return false;
    }

    // Retrieve medicines belonging to this prescription
    $prescription['items'] = $this->getPrescriptionItems(
        $prescriptionId
    );

    return $prescription;
}


}
