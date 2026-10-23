<?php

class Attendance
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn=$conn;
    }

    public function employeeExists($employeeId)
    {
        $stmt=$this->conn->prepare("SELECT 1 FROM employees WHERE id=?");
        $stmt->execute([$employeeId]);

        return $stmt->fetchColumn() !== false;
    }

    public function markAttendance($employeeId,$date,$status,$checkIn,$checkOut)
    {
            if (in_array(strtolower(trim($status)), ["absent", "leave"], true)) {
                $checkIn=null;
                $checkOut=null;
            }

            $sql="INSERT INTO attendance(employee_id,attendance_date,status,check_in,check_out)
                    VALUES(?,?,?,?,?)";
            $stmt=$this->conn->prepare($sql);
            return $stmt->execute([
                $employeeId,
                $date,
                $status,
                $checkIn,
                $checkOut
            ]);
    }
    public function getAttendance()
    {
        $sql="SELECT
                    a.attendance_id,
                    e.name,
                    a.employee_id,
                    a.attendance_date,
                    a.status,
                    a.check_in,
                    a.check_out
                    FROM attendance a
                    JOIN employees e
                    ON a.employee_id=e.id
                    ORDER BY a.attendance_date DESC";

                    $stmt=$this->conn->query($sql);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttendanceByDate($date)
    {
        $sql="SELECT
                    a.attendance_id,
                    e.name,
                    a.employee_id,
                    a.attendance_date,
                    a.status,
                    a.check_in,
                    a.check_out
                    FROM attendance a
                    JOIN employees e
                    ON a.employee_id=e.id
                    WHERE a.attendance_date=?
                    ORDER BY e.name";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([$date]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAttendance($attendanceId,$status,$checkIn,$checkOut)
    {
        if (in_array(strtolower(trim($status)), ["absent", "leave"], true)) {
            $checkIn=null;
            $checkOut=null;
        }

        $sql="UPDATE attendance SET status=?,check_in=?,check_out=? WHERE attendance_id=?";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([
                      $status,
                      $checkIn,
                      $checkOut,
                      $attendanceId
                    ]);
    }

    public function deleteAttendance($attendanceId)
    {
        $sql="DELETE FROM attendance WHERE attendance_id=?";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([$attendanceId]);

    }

}
?>