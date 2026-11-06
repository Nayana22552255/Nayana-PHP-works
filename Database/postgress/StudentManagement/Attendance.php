<?php
class Attendance 
{
    private PDO $conn;
    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function addAttendance(int $studentId, ?string $date, string $status): bool 
    {
        $this->validateStatus($status);

        $sql = "INSERT INTO attendance(student_id, attendance_date, attendance_status)
                VALUES (?, COALESCE(?::DATE, CURRENT_DATE), ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$studentId,$date,$status]);
    }

    public function getAttendance():Array 
    {
        $sql="SELECT 
                    a.attendance_id,
                    a.student_id,
                    s.student_name,
                    s.registration_number,
                    a.attendance_date,
                    a.attendance_status
                    FROM attendance a
                    INNER JOIN students s
                    ON a.student_id=s.student_id
                    ORDER BY a.attendance_date DESC,a.attendance_id DESC";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttendanceById(int $id): ?array
    {
        $sql = "SELECT * FROM attendance WHERE attendance_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

        return $attendance === false ? null : $attendance;
    }

    public function getAttendanceByStudent(int $studentId):array 
    {
        $sql="SELECT 
                    a.attendance_id,
                    a.student_id,
                    s.student_name,
                    a.attendance_date,
                    a.attendance_status
                    FROM attendance a
                    INNER JOIN students s
                    ON a.student_id=s.student_id
                    WHERE a.student_id=?
                    ORDER BY a.attendance_date DESC";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAttendance(int $id,?string $date,string $status): bool 
    {
        $this->validateStatus($status);

        $sql = "UPDATE attendance SET attendance_date = COALESCE(?::DATE, attendance_date),
                attendance_status = ? WHERE attendance_id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([$date,$status,$id]);

        return $stmt->rowCount() > 0;
    }

    public function deleteAttendance(int $id): bool
    {
        $sql = "DELETE FROM attendance WHERE attendance_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }

     public function getAttendanceByDate(string $date): array
    {
        $sql = "SELECT
                    a.attendance_id,
                    a.student_id,
                    s.student_name,
                    s.registration_number,
                    a.attendance_date,
                    a.attendance_status
                    FROM attendance a
                    INNER JOIN students s
                    ON a.student_id = s.student_id
                    WHERE a.attendance_date = ?::DATE
                    ORDER BY s.student_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$date]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttendancePercentage(int $studentId): float 
    {
        $sql="SELECT 
                    COALESCE(
                        ROUND(
                            100.0 * COUNT(*) FILTER (WHERE attendance_status = 'Present')
                            / NULLIF(COUNT(*), 0),
                            2
                        ),
                        0
                    ) AS percentage
                    FROM attendance
                    WHERE student_id = ?";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$studentId]);
        return (float) $stmt->fetchColumn();
    }

    public function getAttendanceSummary(int $studentId): array
    {
        $sql="SELECT
                    COUNT(*) AS total_classes,
                    COUNT(*) FILTER (WHERE attendance_status = 'Present') AS present,
                    COUNT(*) FILTER (WHERE attendance_status = 'Absent') AS absent,
                    COUNT(*) FILTER (WHERE attendance_status = 'Late') AS late
                    FROM attendance
                    WHERE student_id = ?";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalAttendance(): int
    {
        $sql = "SELECT COUNT(*)FROM attendance";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    private function validateStatus(string $status): void
    {
        $allowedStatuses = ['Present','Absent','Late'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException(
                "Invalid attendance status. Use Present, Absent or Late."
            );
        }
    }


}