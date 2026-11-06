
<?php

class Course
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    
    public function addCourse(
        string $name,
        string $duration,
        string $code,
        float $fee,
        ?string $startDate,
        ?string $endDate,
        ?string $description,
        string $status
    ): bool {

        $sql = "INSERT INTO courses
                (
                    course_name,
                    duration,
                    course_code,
                    course_fee,
                    start_date,
                    end_date,
                    description,
                    course_status
                )
                VALUES (?, ?, ?, ?, ?::DATE, ?::DATE, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $name,
            $duration,
            $code,
            $fee,
            $startDate,
            $endDate,
            $description,
            $status
        ]);
    }

    public function getCourse(): array
    {
        $sql = "SELECT *FROM courses ORDER BY course_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCourseById(int $id): ?array
    {
        $sql = "SELECT * FROM courses WHERE course_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        return $course === false ? null : $course;
    }

    public function updateCourse(
        int $id,
        string $name,
        string $duration,
        string $code,
        float $fee,
        ?string $startDate,
        ?string $endDate,
        ?string $description,
        string $status
    ): bool {

        $sql = "UPDATE courses SET
                    course_name = ?,
                    duration = ?,
                    course_code = ?,
                    course_fee = ?,
                    start_date = ?::DATE,
                    end_date = ?::DATE,
                    description = ?,
                    course_status = ?
                    WHERE course_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            $name,
            $duration,
            $code,
            $fee,
            $startDate,
            $endDate,
            $description,
            $status,
            $id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function deleteCourse(int $id): bool
    {
        $sql = "DELETE FROM courses WHERE course_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function searchCourse(string $keyword): array
    {
        $sql = "SELECT * FROM courses WHERE course_name ILIKE ? ORDER BY course_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["%$keyword%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCourseByCode(string $code): ?array
    {
        $sql = "SELECT * FROM courses WHERE course_code = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$code]);

        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        return $course === false ? null : $course;
    }

    public function getActiveCourses(): array
    {
        $sql = "SELECT * FROM courses WHERE course_status = 'Active' ORDER BY course_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCourses(): int
    {
        $sql = "SELECT COUNT(*) FROM courses";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function getCourseStudents(int $courseId): array
    {
        $sql = "SELECT
                    s.student_id,
                    s.student_name,
                    s.email,
                    s.phone,
                    s.registration_number
                    FROM students s
                    INNER JOIN courses c
                    ON s.course_id = c.course_id
                    WHERE c.course_id = ?
                    ORDER BY s.student_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$courseId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
