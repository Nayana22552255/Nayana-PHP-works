
<?php

class User extends Model
{
    // Find user by username
    public function findByUsername($username)
    {
        $sql = "SELECT * FROM users
                WHERE username = :username
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);

        return $stmt->fetch();
    }

    // Find user by email
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch();
    }

    // Find user by ID
    public function findById($id)
    {
        $sql = "SELECT id, username, email, role, status
                FROM users
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    // Create a new user account
    public function createUser($data)
    {
        $sql = "INSERT INTO users
                (username, email, password, role, status)
                VALUES
                (:username, :email, :password, :role, 1)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            ),
            ':role' => $data['role']
        ]);

        return $this->db->lastInsertId();
    }

    // Verify username and password
    public function authenticate($username, $password)
    {
        $user = $this->findByUsername($username);

        if (!$user || $user['status'] != 1) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        return $user;
    }
}
