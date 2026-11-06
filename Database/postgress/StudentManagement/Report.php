
<?php

class Report
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getDashboardCounts(): array
    {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM students) AS total_students,
                    (SELECT COUNT(*) FROM courses) AS total_courses,
                    (SELECT COUNT(*) FROM departments) AS total_departments,
                    (SELECT COUNT(*) FROM attendance) AS total_attendance,
                    (SELECT COUNT(*) FROM marks) AS total_mark_records,
                    (SELECT COUNT(*) FROM fees) AS total_fee_records";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function getStudentStatusReport(): array
    {
        $sql = "SELECT status,
                COUNT(*) AS total_students
                FROM students
                GROUP BY status
                ORDER BY status";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getCourseWiseStudentCount(): array
    {
        $sql = "SELECT
                    c.course_id,
                    c.course_name,
                    c.course_code,
                    COUNT(s.student_id) AS total_students
                    FROM courses c
                    LEFT JOIN students s
                    ON c.course_id = s.course_id
                    GROUP BY
                    c.course_id,
                    c.course_name,
                    c.course_code
                    ORDER BY c.course_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getStudentsWithoutCourse(): array
    {
        $sql = "SELECT
                    student_id,
                    student_name,
                    email,
                    phone
                    FROM students
                    WHERE course_id IS NULL
                    ORDER BY student_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttendanceSummary(): array
    {
        $sql = "SELECT
                    attendance_status,
                    COUNT(*) AS total_records
                    FROM attendance
                    GROUP BY attendance_status
                    ORDER BY attendance_status";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentAttendanceReport(): array
    {
        $sql = "SELECT
                    s.student_id,
                    s.student_name,
                    s.registration_number,
                    COUNT(a.attendance_id) AS total_classes,
                    COUNT(a.attendance_id) 
                    FILTER (WHERE a.attendance_status = 'Present') AS present,
                    COUNT(a.attendance_id) 
                    FILTER (WHERE a.attendance_status = 'Absent') AS absent,
                    COUNT(a.attendance_id) 
                    FILTER (WHERE a.attendance_status = 'Late') AS late,
                    COALESCE(
                        ROUND(
                            100.0 * COUNT(a.attendance_id) FILTER (
                                WHERE a.attendance_status = 'Present'
                            ) / NULLIF(COUNT(a.attendance_id), 0),2),0) AS attendance_percentage
                    FROM students s
                    LEFT JOIN attendance a
                    ON s.student_id = a.student_id
                    GROUP BY
                    s.student_id,
                    s.student_name,
                    s.registration_number
                    ORDER BY s.student_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentMarksReport(): array
    {
        $sql = "SELECT
                    s.student_id,
                    s.student_name,
                    s.registration_number,
                    COALESCE(SUM(m.total_marks), 0) AS total_marks,
                    COALESCE(SUM(m.obtained_marks), 0) AS obtained_marks,
                    COALESCE(
                        ROUND(
                            100.0 * SUM(m.obtained_marks) /
                            NULLIF(SUM(m.total_marks), 0),2),0) AS percentage
                    FROM students s
                    LEFT JOIN marks m
                    ON s.student_id = m.student_id
                    GROUP BY
                    s.student_id,
                    s.student_name,
                    s.registration_number
                    ORDER BY s.student_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getFeeSummary(): array
    {
        $sql = "SELECT
                    COALESCE(SUM(total_fee), 0) AS total_fees,
                    COALESCE(SUM(paid_amount), 0) AS total_paid,
                    COALESCE(SUM(total_fee - paid_amount), 0) AS total_pending
                    FROM fees";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function getStudentFeeReport(): array
    {
        $sql = "SELECT
                    s.student_id,
                    s.student_name,
                    s.registration_number,
                    COALESCE(SUM(f.total_fee), 0) AS total_fee,
                    COALESCE(SUM(f.paid_amount), 0) AS paid_amount,
                    COALESCE(SUM(f.total_fee - f.paid_amount), 0) AS pending_amount
                    FROM students s
                    LEFT JOIN fees f
                    ON s.student_id = f.student_id
                    GROUP BY
                    s.student_id,
                    s.student_name,
                    s.registration_number
                    ORDER BY s.student_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getStudentsByAdmissionDate(string $startDate,string $endDate): array 
    {
        $sql = "SELECT
                    student_id,
                    student_name,
                    registration_number,
                    email,
                    admission_date
                    FROM students
                    WHERE admission_date BETWEEN ?::DATE AND ?::DATE
                    ORDER BY admission_date";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$startDate,$endDate]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function getDepartmentCount(): int
    {
        $sql = "SELECT COUNT(*)FROM departments";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function getTotalStudents(): int
    {
        $sql = "SELECT COUNT(*)FROM students";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    
    public function getTotalCourses(): int
    {
        $sql = "SELECT COUNT(*)FROM courses";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}
