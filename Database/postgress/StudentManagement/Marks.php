
<?php

class Marks
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

   
    public function addMarks(int $studentId, string $subject,string $examType,int $totalMarks,int $obtainedMarks): bool 
    {
        $this->validateMarks($totalMarks, $obtainedMarks);

        $sql = "INSERT INTO marks(
                    student_id,
                    subject_name,
                    exam_type,
                    total_marks,
                    obtained_marks)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$studentId,$subject,$examType,$totalMarks,$obtainedMarks]);
    }

   
    public function getMarks(): array
    {
        $sql = "SELECT
                    m.mark_id,
                    m.student_id,
                    s.student_name,
                    s.registration_number,
                    m.subject_name,
                    m.exam_type,
                    m.total_marks,
                    m.obtained_marks
                    FROM marks m
                    INNER JOIN students s
                    ON m.student_id = s.student_id
                    ORDER BY m.mark_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMarksById(int $id): ?array
    {
        $sql = "SELECT * FROM marks WHERE mark_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        $mark = $stmt->fetch(PDO::FETCH_ASSOC);

        return $mark === false ? null : $mark;
    }

    public function getMarksByStudent(int $studentId): array
    {
        $sql = "SELECT
                    m.mark_id,
                    m.student_id,
                    s.student_name,
                    m.subject_name,
                    m.exam_type,
                    m.total_marks,
                    m.obtained_marks
                    FROM marks m
                    INNER JOIN students s
                    ON m.student_id = s.student_id
                    WHERE m.student_id = ?
                    ORDER BY m.subject_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$studentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function updateMarks(int $id,string $subject,string $examType,int $totalMarks,int $obtainedMarks): bool 
    {
        $this->validateMarks($totalMarks, $obtainedMarks);

        $sql = "UPDATE marks SET subject_name = ?,exam_type = ?,
                total_marks = ?,obtained_marks = ? WHERE mark_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$subject,$examType,$totalMarks,$obtainedMarks,$id]);

        return $stmt->rowCount() > 0;
    }

  
    public function deleteMarks(int $id): bool
    {
        $sql = "DELETE FROM marks WHERE mark_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

    public function getStudentPercentage(int $studentId): float
    {
        $sql = "SELECT
                    COALESCE(
                        ROUND(
                            100.0 * SUM(obtained_marks) /
                            NULLIF(SUM(total_marks), 0),2 ), 0 ) AS percentage
                            FROM marks
                            WHERE student_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$studentId]);

        return (float) $stmt->fetchColumn();
    }


    public function getStudentTotalMarks(int $studentId): array
    {
        $sql = "SELECT
                COALESCE(SUM(total_marks), 0) AS total_marks,
                COALESCE(SUM(obtained_marks), 0) AS obtained_marks
                FROM marks
                WHERE student_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$studentId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStudentGrade(int $studentId): string
    {
        $percentage = $this->getStudentPercentage($studentId);

        if ($percentage >= 90) {
            return "A+";
        } elseif ($percentage >= 80) {
            return "A";
        } elseif ($percentage >= 70) {
            return "B";
        } elseif ($percentage >= 60) {
            return "C";
        } elseif ($percentage >= 50) {
            return "D";
        } else {
            return "F";
        }
    }


    public function getSubjectMarks(string $subject): array
    {
        $sql = "SELECT
                    m.mark_id,
                    s.student_name,
                    s.registration_number,
                    m.subject_name,
                    m.exam_type,
                    m.total_marks,
                    m.obtained_marks
                    FROM marks m
                    INNER JOIN students s
                    ON m.student_id = s.student_id
                    WHERE m.subject_name ILIKE ?
                    ORDER BY s.student_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["%$subject%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getTotalMarkRecords(): int
    {
        $sql = "SELECT COUNT(*) FROM marks";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    private function validateMarks( int $totalMarks,int $obtainedMarks): void 
    {

        if ($totalMarks <= 0) {
            throw new InvalidArgumentException(
                "Total marks must be greater than zero."
            );
        }

        if ($obtainedMarks < 0 || $obtainedMarks > $totalMarks) {
            throw new InvalidArgumentException(
                "Obtained marks must be between 0 and total marks."
            );
        }
    }
}
