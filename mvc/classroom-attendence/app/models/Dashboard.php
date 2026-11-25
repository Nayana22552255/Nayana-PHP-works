
<?php

class Dashboard extends Model
{
    // Total registered students
    public function getTotalStudents()
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM students"
        );

        return (int) $stmt->fetchColumn();
    }

    // Total registered teachers
    public function getTotalTeachers()
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM teachers"
        );

        return (int) $stmt->fetchColumn();
    }

    // Students present today
    public function getTodayPresent()
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*)
             FROM attendance
             WHERE attendance_date = CURRENT_DATE
             AND status = 'Present'"
        );

        return (int) $stmt->fetchColumn();
    }

    // Students absent today
    public function getTodayAbsent()
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*)
             FROM attendance
             WHERE attendance_date = CURRENT_DATE
             AND status = 'Absent'"
        );

        return (int) $stmt->fetchColumn();
    }

    // Latest attendance records
    public function getRecentAttendance()
    {
        $sql = "SELECT
                    s.name,
                    s.admission_no,
                    a.attendance_date,
                    a.status
                FROM attendance a
                INNER JOIN students s
                    ON a.student_id = s.id
                ORDER BY a.attendance_date DESC, a.id DESC
                LIMIT 10";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }
}
