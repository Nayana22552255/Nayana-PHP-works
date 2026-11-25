
<?php

class Doctor extends Model
{
    // Get all doctors with user and department information
    public function getAllDoctors()
    {
        $sql = "SELECT
                    d.*,
                    u.username,
                    u.email,
                    dep.department_name
                FROM doctors d

                INNER JOIN users u
                    ON d.user_id = u.id

                INNER JOIN departments dep
                    ON d.department_id = dep.id

                ORDER BY d.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    // Find doctor by ID
    public function getDoctorById($id)
    {
        $sql = "SELECT
                    d.*,
                    u.username,
                    u.email
                FROM doctors d

                INNER JOIN users u
                    ON d.user_id = u.id

                WHERE d.id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    // Get active departments for doctor form
    public function getDepartments()
    {
        $sql = "SELECT id, department_name
                FROM departments
                WHERE status = 1
                ORDER BY department_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    // Create doctor and login account
    public function addDoctor($data)
    {
        try {

            $this->db->beginTransaction();

            // Create login account
            $userSql = "INSERT INTO users
                        (username, email, password, role, status)
                        VALUES
                        (:username, :email, :password,
                         'doctor', 1)";

            $userStmt = $this->db->prepare($userSql);

            $userStmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => password_hash(
                    $data['password'],
                    PASSWORD_DEFAULT
                )
            ]);

            // Get newly created user ID
            $userId = $this->db->lastInsertId();


            // Create doctor profile
            $doctorSql = "INSERT INTO doctors (
                            user_id,
                            department_id,
                            first_name,
                            last_name,
                            gender,
                            date_of_birth,
                            phone,
                            address,
                            qualification,
                            specialization,
                            experience,
                            registration_number
                          )
                          VALUES (
                            :user_id,
                            :department_id,
                            :first_name,
                            :last_name,
                            :gender,
                            :date_of_birth,
                            :phone,
                            :address,
                            :qualification,
                            :specialization,
                            :experience,
                            :registration_number
                          )";

            $doctorStmt = $this->db->prepare($doctorSql);

            $doctorStmt->execute([
                ':user_id' => $userId,
                ':department_id' => $data['department_id'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':gender' => $data['gender'],
                ':date_of_birth' => $data['date_of_birth'],
                ':phone' => $data['phone'],
                ':address' => $data['address'],
                ':qualification' => $data['qualification'],
                ':specialization' => $data['specialization'],
                ':experience' => $data['experience'],
                ':registration_number' => $data['registration_number']
            ]);

            // Save both records
            $this->db->commit();

            return true;

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }


    // Update doctor information
    public function updateDoctor($id, $data)
    {
        try {

            $this->db->beginTransaction();

            // Get associated user ID
            $doctor = $this->getDoctorById($id);

            if (!$doctor) {
                throw new RuntimeException(
                    'Doctor not found.'
                );
            }

            // Update login email
            $userSql = "UPDATE users
                        SET email = :email
                        WHERE id = :user_id
                        AND role = 'doctor'";

            $userStmt = $this->db->prepare($userSql);

            $userStmt->execute([
                ':email' => $data['email'],
                ':user_id' => $doctor['user_id']
            ]);


            // Update doctor details
            $sql = "UPDATE doctors SET

                        department_id = :department_id,
                        first_name = :first_name,
                        last_name = :last_name,
                        gender = :gender,
                        date_of_birth = :date_of_birth,
                        phone = :phone,
                        address = :address,
                        qualification = :qualification,
                        specialization = :specialization,
                        experience = :experience,
                        registration_number = :registration_number

                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':department_id' => $data['department_id'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':gender' => $data['gender'],
                ':date_of_birth' => $data['date_of_birth'],
                ':phone' => $data['phone'],
                ':address' => $data['address'],
                ':qualification' => $data['qualification'],
                ':specialization' => $data['specialization'],
                ':experience' => $data['experience'],
                ':registration_number' => $data['registration_number'],
                ':id' => $id
            ]);

            $this->db->commit();

            return true;

        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }


    // Activate or deactivate doctor
    public function changeStatus($id, $status)
    {
        try {

            $this->db->beginTransaction();

            $doctor = $this->getDoctorById($id);

            if (!$doctor) {
                throw new RuntimeException(
                    'Doctor not found.'
                );
            }

            // Update doctor status
            $sql = "UPDATE doctors
                    SET status = :status
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':status' => $status,
                ':id' => $id
            ]);

            // Update login account status
            $userSql = "UPDATE users
                        SET status = :status
                        WHERE id = :user_id
                        AND role = 'doctor'";

            $userStmt = $this->db->prepare($userSql);

            $userStmt->execute([
                ':status' => $status,
                ':user_id' => $doctor['user_id']
            ]);

            $this->db->commit();

            return true;

        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }


    // Reset doctor's login password
    public function resetDoctorPassword($doctorId, $newPassword)
    {
        // Find the doctor and associated login account
        $sql = "SELECT user_id
                FROM doctors
                WHERE id = :doctor_id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':doctor_id' => $doctorId
        ]);

        $doctor = $stmt->fetch();

        if (!$doctor) {
            throw new RuntimeException('Doctor not found.');
        }

        // Generate a secure password hash
        $hashedPassword = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );

        // Update the login account password
        $updateSql = "UPDATE users
                      SET password = :password
                      WHERE id = :user_id
                      AND role = 'doctor'";

        $updateStmt = $this->db->prepare($updateSql);

        $updateStmt->execute([
            ':password' => $hashedPassword,
            ':user_id' => $doctor['user_id']
        ]);

        if ($updateStmt->rowCount() === 0) {
            // Confirm whether the account exists even if the hash was unchanged
            $checkSql = "SELECT id
                         FROM users
                         WHERE id = :user_id
                         AND role = 'doctor'";

            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([
                ':user_id' => $doctor['user_id']
            ]);

            if (!$checkStmt->fetch()) {
                throw new RuntimeException('Doctor login account not found.');
            }
        }

        return true;
    }
    // Get all active doctors for patient dashboard
public function getActiveDoctors()
{
    $sql = "SELECT
                d.id,
                d.first_name,
                d.last_name,
                d.qualification,
                d.specialization,
                d.experience,
                d.phone,
                dep.department_name

            FROM doctors d

            INNER JOIN users u
                ON d.user_id = u.id

            INNER JOIN departments dep
                ON d.department_id = dep.id

            WHERE d.status = 1
              AND u.status = 1
              AND dep.status = 1

            ORDER BY d.first_name ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}

// Get doctor record using logged-in user ID
public function getDoctorByUserId($userId)
{
    $sql = "SELECT *
            FROM doctors
            WHERE user_id = :user_id
            LIMIT 1";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':user_id' => $userId
    ]);

    return $stmt->fetch();
}

}
