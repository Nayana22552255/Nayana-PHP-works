
<?php

class Nurse extends Model
{
    // Get all nurses, with optional search
    
      
// Get all nurses, with optional search
public function getAllNurses($keyword = '')
{
    $sql = "
        SELECT
            n.*,
            d.department_name,
            u.username
        FROM nurses n
        INNER JOIN departments d
            ON n.department_id = d.id
        INNER JOIN users u
            ON n.user_id = u.id
    ";

    if (!empty($keyword)) {
        $sql .= "
            WHERE
                n.nurse_number LIKE :keyword1
                OR n.first_name LIKE :keyword2
                OR n.last_name LIKE :keyword3
                OR n.phone LIKE :keyword4
                OR d.department_name LIKE :keyword5
                OR u.username LIKE :keyword6
        ";
    }

    $sql .= " ORDER BY n.id DESC";

    $stmt = $this->db->prepare($sql);

    if (!empty($keyword)) {
        $search = '%' . $keyword . '%';

        $stmt->execute([
            ':keyword1' => $search,
            ':keyword2' => $search,
            ':keyword3' => $search,
            ':keyword4' => $search,
            ':keyword5' => $search,
            ':keyword6' => $search
        ]);
    } else {
        $stmt->execute();
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    

    // Get nurse by ID
    public function getNurseById($id)
    {
        $sql = "
            SELECT
                n.*,
                d.department_name,
                u.username
            FROM nurses n
            INNER JOIN departments d
                ON n.department_id = d.id
            INNER JOIN users u
                ON n.user_id = u.id
            WHERE n.id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get active departments
    public function getActiveDepartments()
    {
        $sql = "
            SELECT id, department_name
            FROM departments
            WHERE status = 1
            ORDER BY department_name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get nurse-role users not already linked to a nurse
    public function getAvailableNurseUsers()
    {
        $sql = "
            SELECT u.id, u.username, u.email
            FROM users u
            WHERE u.role = 'nurse'
            AND NOT EXISTS (
                SELECT 1
                FROM nurses n
                WHERE n.user_id = u.id
            )
            ORDER BY u.username ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new nurse
    
    // Add a new nurse and automatically create a login account
    public function addNurse($data)
    {
        try {
            $this->db->beginTransaction();

            // Create the nurse login account
            $userSql = "
                INSERT INTO users (
                    username,
                    email,
                    password,
                    role,
                    status
                )
                VALUES (
                    :username,
                    :email,
                    :password,
                    'nurse',
                    1
                )
            ";

            $userStmt = $this->db->prepare($userSql);

            $userStmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => password_hash(
                    $data['password'],
                    PASSWORD_DEFAULT
                )
            ]);

            // Get the newly created user ID
            $userId = $this->db->lastInsertId();

            // Create the nurse profile linked to the user account
            $nurseSql = "
                INSERT INTO nurses (
                    nurse_number,
                    user_id,
                    department_id,
                    first_name,
                    last_name,
                    gender,
                    qualification,
                    phone,
                    email,
                    experience_years,
                    shift,
                    address
                )
                VALUES (
                    :nurse_number,
                    :user_id,
                    :department_id,
                    :first_name,
                    :last_name,
                    :gender,
                    :qualification,
                    :phone,
                    :email,
                    :experience_years,
                    :shift,
                    :address
                )
            ";

            $nurseStmt = $this->db->prepare($nurseSql);

            $nurseStmt->execute([
                ':nurse_number' => $data['nurse_number'],
                ':user_id' => $userId,
                ':department_id' => $data['department_id'],
                ':first_name' => $data['first_name'],
                ':last_name' => $data['last_name'],
                ':gender' => $data['gender'],
                ':qualification' => $data['qualification'],
                ':phone' => $data['phone'],
                ':email' => $data['email'],
                ':experience_years' => $data['experience_years'],
                ':shift' => $data['shift'],
                ':address' => $data['address']
            ]);

            $this->db->commit();

            return $userId;

        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }
    // Update nurse details
    public function updateNurse($id, $data)
    {
        $sql = "
            UPDATE nurses SET
                department_id = :department_id,
                first_name = :first_name,
                last_name = :last_name,
                gender = :gender,
                qualification = :qualification,
                phone = :phone,
                email = :email,
                experience_years = :experience_years,
                shift = :shift,
                address = :address
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':department_id' => $data['department_id'],
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':gender' => $data['gender'],
            ':qualification' => $data['qualification'],
            ':phone' => $data['phone'],
            ':email' => $data['email'],
            ':experience_years' => $data['experience_years'],
            ':shift' => $data['shift'],
            ':address' => $data['address'],
            ':id' => $id
        ]);
    }

    // Activate or deactivate nurse
    public function updateStatus($id, $status)
    {
        $sql = "
            UPDATE nurses
            SET status = :status
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }
}
