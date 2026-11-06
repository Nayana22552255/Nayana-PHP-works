
<?php

class Student
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

  
    public function addStudent(
        string $name,
        int $course_id,
        string $address,
        ?string $email,
        ?string $phone,
        ?string $dob,
        ?string $gender,
        ?string $admission_date,
        string $status,
        ?string $registration_number
    ): bool {

        $sql = "INSERT INTO students
                (
                    student_name,
                    course_id,
                    address,
                    email,
                    phone,
                    date_of_birth,
                    gender,
                    admission_date,
                    status,
                    registration_number
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, COALESCE(?::DATE, CURRENT_DATE), ?, ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $name,
            $course_id,
            $address,
            $email,
            $phone,
            $dob,
            $gender,
            $admission_date,
            $status,
            $registration_number
        ]);
    }

    public function getStudents(): array
    {
        $sql = "SELECT
                    s.student_id,
                    s.student_name,
                    s.course_id,
                    c.course_name,
                    s.address,
                    s.email,
                    s.phone,
                    s.date_of_birth,
                    s.gender,
                    s.admission_date,
                    s.status,
                    s.registration_number
                    FROM students s
                    INNER JOIN courses c
                    ON s.course_id = c.course_id
                    ORDER BY s.student_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getStudentById(int $id): ?array
    {
        $sql = "SELECT s.*,c.course_name
                FROM students s
                INNER JOIN courses c
                ON s.course_id = c.course_id
                WHERE s.student_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        return $student === false ? null : $student;
    }

   
    public function updateStudent(
        int $id,
        string $name,
        int $course_id,
        string $address,
        ?string $email,
        ?string $phone,
        ?string $dob,
        ?string $gender,
        ?string $admission_date,
        string $status,
        ?string $registration_number
    ): bool {

        $sql = "UPDATE students SET
                    student_name = ?,
                    course_id = ?,
                    address = ?,
                    email = ?,
                    phone = ?,
                    date_of_birth = ?,
                    gender = ?,
                    admission_date = COALESCE(?::DATE, admission_date),
                    status = ?,
                    registration_number = ?
                    WHERE student_id = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $name,
            $course_id,
            $address,
            $email,
            $phone,
            $dob,
            $gender,
            $admission_date,
            $status,
            $registration_number,
            $id
        ]);
    }

   
    public function deleteStudent(int $id): bool
    {
        $sql = "DELETE FROM students WHERE student_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    
    public function searchStudents(string $keyword): array
    {
        $sql = "SELECT
                    s.student_id,
                    s.student_name,
                    s.course_id,
                    c.course_name,
                    s.address,
                    s.email,
                    s.phone,
                    s.date_of_birth,
                    s.gender,
                    s.admission_date,
                    s.status,
                    s.registration_number
                    FROM students s
                    INNER JOIN courses c
                    ON s.course_id = c.course_id
                    WHERE s.student_name ILIKE ?
                    ORDER BY s.student_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["%$keyword%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentByRegistration(string $regNo): ?array
    {
        $sql = "SELECT * FROM students WHERE registration_number = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$regNo]);

        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        return $student === false ? null : $student;
    }

    
    public function getStudentsByCourse(int $course_id): array
    {
        $sql = "SELECT s.*,c.course_name
                FROM students s
                INNER JOIN courses c
                ON s.course_id = c.course_id
                WHERE s.course_id = ?
                ORDER BY s.student_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$course_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getTotalStudents(): int
    {
        $sql = "SELECT COUNT(*) FROM students";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function getActiveStudents(): array
    {
        $sql = "SELECT * FROM students WHERE status = 'Active'ORDER BY student_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
