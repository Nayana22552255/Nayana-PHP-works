
<?php

class Patient extends Model
{
    // Display all patients
    public function getAllPatients()
    {
        $sql = "SELECT *
                FROM patients
                ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    // Find patient by database ID
    public function getPatientById($id)
    {
        $sql = "SELECT *
                FROM patients
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    // Find patient using registration number
    public function getPatientByNumber($patientNumber)
    {
        $sql = "SELECT *
                FROM patients
                WHERE patient_number = :patient_number";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':patient_number' => $patientNumber
        ]);

        return $stmt->fetch();
    }


    // Register a new patient
    public function addPatient($data)
    {
        try {

            $this->db->beginTransaction();

            // Initially insert a temporary unique number.
            // It will be replaced using the generated database ID.
            $sql = "INSERT INTO patients (

                        patient_number,
                        first_name,
                        last_name,
                        gender,
                        date_of_birth,
                        blood_group,
                        phone,
                        email,
                        address,
                        emergency_contact_name,
                        emergency_contact_phone

                    )
                    VALUES (

                        :patient_number,
                        :first_name,
                        :last_name,
                        :gender,
                        :date_of_birth,
                        :blood_group,
                        :phone,
                        :email,
                        :address,
                        :emergency_contact_name,
                        :emergency_contact_phone

                    )";

            $stmt = $this->db->prepare($sql);

            $temporaryNumber = 'TEMP-' . bin2hex(random_bytes(12));

            $stmt->execute([

                ':patient_number' => $temporaryNumber,

                ':first_name' => $data['first_name'],

                ':last_name' => $data['last_name'],

                ':gender' => $data['gender'],

                ':date_of_birth' => $data['date_of_birth'],

                ':blood_group' => $data['blood_group'],

                ':phone' => $data['phone'],

                ':email' => $data['email'] ?: null,

                ':address' => $data['address'],

                ':emergency_contact_name' =>
                    $data['emergency_contact_name'],

                ':emergency_contact_phone' =>
                    $data['emergency_contact_phone']

            ]);


            // Get newly generated database ID
            $patientId = $this->db->lastInsertId();


            // Generate patient registration number
            $patientNumber =
                'PAT' .
                date('Y') .
                str_pad($patientId, 6, '0', STR_PAD_LEFT);


            // Update generated patient number
            $updateSql = "UPDATE patients
                          SET patient_number = :patient_number
                          WHERE id = :id";

            $updateStmt = $this->db->prepare($updateSql);

            $updateStmt->execute([

                ':patient_number' => $patientNumber,

                ':id' => $patientId

            ]);


            // Commit transaction
            $this->db->commit();

            return $patientNumber;

        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }



    // Register a patient linked to a user account
    public function addPatientForUser($userId, $data)
    {
        // Insert patient with a temporary registration number
        $temporaryNumber = 'TEMP-' . bin2hex(random_bytes(12));

        $sql = "INSERT INTO patients (
                    user_id,
                    patient_number,
                    first_name,
                    last_name,
                    gender,
                    date_of_birth,
                    blood_group,
                    phone,
                    email,
                    address,
                    emergency_contact_name,
                    emergency_contact_phone
                )
                VALUES (
                    :user_id,
                    :patient_number,
                    :first_name,
                    :last_name,
                    :gender,
                    :date_of_birth,
                    :blood_group,
                    :phone,
                    :email,
                    :address,
                    :emergency_contact_name,
                    :emergency_contact_phone
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':user_id' => $userId,
            ':patient_number' => $temporaryNumber,
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':gender' => $data['gender'],
            ':date_of_birth' => $data['date_of_birth'],
            ':blood_group' => $data['blood_group'],
            ':phone' => $data['phone'],
            ':email' => $data['email'] ?: null,
            ':address' => $data['address'] ?: null,
            ':emergency_contact_name' =>
                $data['emergency_contact_name'] ?: null,
            ':emergency_contact_phone' =>
                $data['emergency_contact_phone'] ?: null
        ]);

        // Get the newly generated patient ID
        $patientId = $this->db->lastInsertId();

        // Generate patient registration number
        $patientNumber = 'PAT' . date('Y') .
            str_pad($patientId, 6, '0', STR_PAD_LEFT);

        // Update patient registration number
        $updateSql = "UPDATE patients
                      SET patient_number = :patient_number
                      WHERE id = :id";

        $updateStmt = $this->db->prepare($updateSql);

        $updateStmt->execute([
            ':patient_number' => $patientNumber,
            ':id' => $patientId
        ]);

        return $patientNumber;
    }





    // Update patient information
    public function updatePatient($id, $data)
    {
        $sql = "UPDATE patients SET

                    first_name = :first_name,

                    last_name = :last_name,

                    gender = :gender,

                    date_of_birth = :date_of_birth,

                    blood_group = :blood_group,

                    phone = :phone,

                    email = :email,

                    address = :address,

                    emergency_contact_name =
                        :emergency_contact_name,

                    emergency_contact_phone =
                        :emergency_contact_phone

                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([

            ':first_name' => $data['first_name'],

            ':last_name' => $data['last_name'],

            ':gender' => $data['gender'],

            ':date_of_birth' => $data['date_of_birth'],

            ':blood_group' => $data['blood_group'],

            ':phone' => $data['phone'],

            ':email' => $data['email'] ?: null,

            ':address' => $data['address'],

            ':emergency_contact_name' =>
                $data['emergency_contact_name'],

            ':emergency_contact_phone' =>
                $data['emergency_contact_phone'],

            ':id' => $id

        ]);
    }


    // Search patients
    
// Search patients
public function searchPatients($keyword)
{
    $sql = "SELECT *
            FROM patients

            WHERE patient_number LIKE :patient_number

            OR first_name LIKE :first_name

            OR last_name LIKE :last_name

            OR phone LIKE :phone

            OR email LIKE :email

            ORDER BY id DESC";

    $stmt = $this->db->prepare($sql);

    $search = '%' . trim($keyword) . '%';

    $stmt->execute([
        ':patient_number' => $search,
        ':first_name'     => $search,
        ':last_name'      => $search,
        ':phone'          => $search,
        ':email'          => $search
    ]);

    return $stmt->fetchAll();
}


    // Activate or deactivate patient
    public function changeStatus($id, $status)
    {
        $sql = "UPDATE patients
                SET status = :status
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([

            ':status' => $status,

            ':id' => $id

        ]);
    }

    // Get patients assigned to a particular doctor
public function getPatientsByDoctor($doctorId)
{
    $sql = "SELECT
                p.*,
                appointment_data.last_appointment,
                appointment_data.total_appointments

            FROM patients p

            INNER JOIN (
                SELECT
                    patient_id,
                    MAX(appointment_date) AS last_appointment,
                    COUNT(*) AS total_appointments

                FROM appointments

                WHERE doctor_id = :doctor_id

                GROUP BY patient_id

            ) AS appointment_data

            ON p.id = appointment_data.patient_id

            ORDER BY appointment_data.last_appointment DESC";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':doctor_id' => $doctorId
    ]);

    return $stmt->fetchAll();
}

// Get a patient's details only if they have an appointment with this doctor
public function getPatientForDoctor($patientId, $doctorId)
{
    $sql = "SELECT DISTINCT p.*
            FROM patients p
            INNER JOIN appointments a
                ON p.id = a.patient_id
            WHERE p.id = :patient_id
              AND a.doctor_id = :doctor_id
            LIMIT 1";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':patient_id' => $patientId,
        ':doctor_id' => $doctorId
    ]);

    return $stmt->fetch();
}

}
