<?php
require_once "Database.php";
require_once "Employee.php";
require_once "Department.php";
require_once "attendance.php";

try
{
     $database=new Database();
     $conn=$database->connect();
     $employee=new Employee($conn);
     $department=new Department($conn);
     $attendance=new Attendance($conn);

     while(true)
    {
     echo "\n-----------Employee Management System--------\n";
     echo "1.Add Employee\n";
     echo "2.View Employees\n";
     echo "3.Update Employees\n";
     echo "4.Delete Employees\n";
     echo "5.Add Department\n";
     echo "6.View Department\n";
     echo "7.Update Department\n";
     echo "8.Delete Department\n";
     echo "9.Mark Attendance\n";
     echo "10.View Atendance\n";
     echo "11.View Attendance By Date\n";
     echo "12.Update Attendance\n";
     echo "13.Delete Attendance\n";
     echo "14.Exit\n";
     echo "-------------------------------------------------\n";

    $choice=readline("enter your choice");
    switch($choice)
    {
        case 1:
                    $name=readline("Enter the employee name: ");
                    $email=readline("Enter the email: ");
                    $employeeDepartment=readline("Enter the department: ");
                    $salary=readline("Enter the salary: ");
                    $employee->addEmployees($name,$email,$employeeDepartment,$salary);

                    echo "Employees added successfully.\n";
                    break;

        case 2:
                 $employees=$employee->getEmployees();
                 
                 if(empty($employees))
                    {
                        echo "No employees found.\n";
                        break;
                    }

                foreach($employees as $emp)
                {
                        echo "\n------------------\n";
                        echo "ID: ".$emp["id"]. "\n";
                        echo "Name: ".$emp["name"]. "\n";
                        echo "Email: ".$emp["email"]. "\n";
                        echo "Department: ".$emp["department"]. "\n";
                        echo "Salary: ".$emp["salary"]. "\n";
                }
            break;

        case 3:
                        $id=(int)readline("Enter the Id: ");
                        $name=readline("enter the name: ");
                        $email=readline("enter the email: ");
                        $department=readline("enter the department: ");
                        $salary=(float)readline("enter the salary: ");

                        $employee->updateEmployee($id,$name,$email,$department,$salary);
                        break;

        case 4:

                        $id=(int)readline("enter the employee id to delete: ");
                        $employee->deleteEmployees($id);
                        echo "Employee deleted successfully\n";
                        break;

        case 5:
                        $department_name=readline("enter the department name");
                        $department->addDepartment($department_name);
                        echo "Department added successfully\n";
                        break;

        case 6:
                        $departments=$department->getDepartment();
                        if(empty($departments))
                            {
                                echo "No department found\n";
                                break;
                            }

                        foreach($departments as $dep)
                            {
                                echo "ID: ".$dep["department_id"]. "\n";
                                echo "Department Name: ".$dep["department_name"]. "\n";

                            }
                        break;

        case 7:
                        $department_id=(int)readline("enter the department id: ");
                        $department_name=readline("enter the department name: ");
                        $department->updateDepartment($department_id,$department_name);
                        break;

        case 8:
                        $department_id=readline("enter the department id to delete a department: ");
                        $department->deleteDepartment($department_id);
                        echo "Department deleted successfully\n";
                        break;

        case 9:
                        $employeeId=(int)readline("Enter the employee ID: ");
                        if (!$attendance->employeeExists($employeeId)) {
                            echo "Employee ID {$employeeId} was not found. Add the employee or enter an existing employee ID.\n";
                            break;
                        }

                        $date=readline("Enter the attendance date (YYYY-MM-DD): ");
                        $status=trim(readline("Enter the status (Present/Absent/Leave): "));
                        if (in_array(strtolower($status), ["absent", "leave"], true)) {
                            $checkIn=null;
                            $checkOut=null;
                        } else {
                            $checkIn=readline("Enter check-in time (HH:MM:SS): ");
                            $checkOut=readline("Enter check-out time (HH:MM:SS): ");
                        }
                        $attendance->markAttendance(
                            $employeeId,
                            $date,
                            $status,
                            $checkIn,
                            $checkOut
                        );
                        echo "Attendance marked successfully.\n";
                        break;

        case 10:
                        $records=$attendance->getAttendance();
                        if(empty($records))
                        {
                            echo "No attendance records found.\n";
                            break;
                        }

                        foreach($records as $record)
                        {
                            echo "\n------------------\n";
                            echo "Attendance ID: ".$record["attendance_id"]."\n";
                            echo "Employee: ".$record["name"]." (ID: ".$record["employee_id"].")\n";
                            echo "Date: ".$record["attendance_date"]."\n";
                            echo "Status: ".$record["status"]."\n";
                            echo "CheckIn: ".$record["check_in"]."\n";
                            echo "CheckOut: ".$record["check_out"]."\n";
                        }
                        break;

        case 11:
                        $date=readline("Enter the date to view (YYYY-MM-DD): ");
                        $records=$attendance->getAttendanceByDate($date);
                        if(empty($records))
                        {
                            echo "No attendance records found for that date.\n";
                            break;
                        }

                        foreach($records as $record)
                        {
                            echo "\n------------------\n";
                            echo "Attendance ID: ".$record["attendance_id"]."\n";
                            echo "Employee: ".$record["name"]." (ID: ".$record["employee_id"].")\n";
                            echo "Date: ".$record["attendance_date"]."\n";
                            echo "Status: ".$record["status"]."\n";
                            echo "CheckIn: ".$record["check_in"]."\n";
                            echo "CheckOut: ".$record["check_out"]."\n";
                        }
                        break;

        case 12:
                        $attendanceId=(int)readline("Enter the attendance ID to update: ");
                        $status=trim(readline("Enter the new status (Present/Absent/Leave): "));
                        if (in_array(strtolower($status), ["absent", "leave"], true)) {
                            $checkIn=null;
                            $checkOut=null;
                        } else {
                            $checkIn=readline("Enter the new check-in time (HH:MM:SS): ");
                            $checkOut=readline("Enter the new check-out time (HH:MM:SS): ");
                        }
                        $attendance->updateAttendance(
                            $attendanceId,
                            $status,
                            $checkIn,
                            $checkOut
                        );
                        echo "Attendance updated successfully.\n";
                        break;

        case 13:
                        $attendanceId=(int)readline("Enter the attendance ID to delete: ");
                        $attendance->deleteAttendance($attendanceId);
                        echo "Attendance deleted successfully.\n";
                        break;

        case 14:
                        echo "Thank U for using this System\n";
                        exit;

        default:
                        echo "Invalid choice. please select a right one\n";}

    }


}catch(PDOException $e)
{
    echo "Database error!".$e->getMessage(). "\n";
}

?>