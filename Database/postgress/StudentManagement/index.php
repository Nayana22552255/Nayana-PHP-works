<?php

require_once 'Database.php';
require_once 'Student.php';
require_once 'Course.php';
require_once 'Department.php';
require_once 'Attendance.php';
require_once 'Marks.php';
require_once 'Fees.php';
require_once 'Report.php';



function input(string $label): string
{
    echo $label;
    $value = fgets(STDIN);
    return $value === false ? '' : trim($value);
}

function inputInt(string $label): ?int
{
    $value = filter_var(input($label), FILTER_VALIDATE_INT);
    return ($value !== false && $value > 0) ? $value : null;
}

function inputFloat(string $label): ?float
{
    $value = input($label);
    if ($value === '' || !is_numeric($value)) {
        return null;
    }
    return (float) $value;
}

function showData(array $data): void
{
    if (empty($data)) {
        echo "No records found.\n";
        return;
    }

    foreach ($data as $row) {
        foreach ($row as $key => $value) {
            echo ucfirst(str_replace('_', ' ', $key)) . ": "
                . ($value === null ? "NULL" : $value) . "\n";
        }
        echo "----------------------------------------\n";
    }
}

function showMenu(string $title, array $options): string
{
    echo "\n========================================\n";
    echo $title . "\n";
    echo "========================================\n";

    foreach ($options as $key => $label) {
        echo $key . ". " . $label . "\n";
    }

    return input("Select an option: ");
}

/* ------------------------- STUDENT SECTION ------------------------- */

function studentMenu(Student $student, Course $course): void
{
    while (true) {
        $choice = showMenu("STUDENT MANAGEMENT", [
            1 => "Display all students",
            2 => "Add student",
            3 => "Find student by ID",
            4 => "Search students by name",
            5 => "Update student",
            6 => "Delete student",
            7 => "Search by registration number",
            8 => "Display students by course",
            9 => "Display active students",
            0 => "Back to main menu"
        ]);

        if ($choice === '0') return;

        try {
            switch ($choice) {
                case '1':
                    showData($student->getStudents());
                    break;

                case '2':
                    $courses = $course->getCourse();
                    if (!$courses) {
                        echo "Add a course before adding a student.\n";
                        break;
                    }
                    echo "\nAvailable courses:\n";
                    showData($courses);

                    $name = input("Student name: ");
                    $courseId = inputInt("Course ID: ");
                    $address = input("Address: ");
                    $email = input("Email (leave blank if unavailable): ");
                    $phone = input("Phone (leave blank if unavailable): ");
                    $dob = input("Date of birth YYYY-MM-DD (blank if unavailable): ");
                    $gender = input("Gender (blank if unavailable): ");
                    $admissionDate = input("Admission date YYYY-MM-DD (blank for today): ");
                    $status = input("Status [Active/Inactive] (default Active): ");
                    $registration = input("Registration number (blank if unavailable): ");

                    if ($name === '' || $courseId === null || $address === '') {
                        echo "Name, valid course ID and address are required.\n";
                        break;
                    }

                    if (!$course->getCourseById($courseId)) {
                        echo "Course ID does not exist.\n";
                        break;
                    }

                    $ok = $student->addStudent(
                        $name, $courseId, $address,
                        $email === '' ? null : $email,
                        $phone === '' ? null : $phone,
                        $dob === '' ? null : $dob,
                        $gender === '' ? null : $gender,
                        $admissionDate === '' ? null : $admissionDate,
                        $status === '' ? 'Active' : $status,
                        $registration === '' ? null : $registration
                    );
                    echo $ok ? "Student added successfully.\n" : "Student was not added.\n";
                    break;

                case '3':
                    $id = inputInt("Student ID: ");
                    if ($id === null) { echo "Invalid student ID.\n"; break; }
                    $record = $student->getStudentById($id);
                    $record ? showData([$record]) : print("Student not found.\n");
                    break;

                case '4':
                    $keyword = input("Enter student name to search: ");
                    showData($student->searchStudents($keyword));
                    break;

                case '5':
                    $id = inputInt("Student ID to update: ");
                    if ($id === null) { echo "Invalid student ID.\n"; break; }
                    if (!$student->getStudentById($id)) {
                        echo "Student not found.\n";
                        break;
                    }
                    $name = input("New student name: ");
                    $courseId = inputInt("New course ID: ");
                    $address = input("New address: ");
                    $email = input("New email (blank if unavailable): ");
                    $phone = input("New phone (blank if unavailable): ");
                    $dob = input("New date of birth YYYY-MM-DD (blank if unavailable): ");
                    $gender = input("New gender (blank if unavailable): ");
                    $admissionDate = input("New admission date YYYY-MM-DD (blank to keep existing): ");
                    $status = input("New status [Active/Inactive]: ");
                    $registration = input("New registration number (blank if unavailable): ");

                    if ($name === '' || $courseId === null || $address === '') {
                        echo "Name, valid course ID and address are required.\n";
                        break;
                    }
                    if (!$course->getCourseById($courseId)) {
                        echo "Course ID does not exist.\n";
                        break;
                    }

                    $ok = $student->updateStudent(
                        $id, $name, $courseId, $address,
                        $email === '' ? null : $email,
                        $phone === '' ? null : $phone,
                        $dob === '' ? null : $dob,
                        $gender === '' ? null : $gender,
                        $admissionDate === '' ? null : $admissionDate,
                        $status === '' ? 'Active' : $status,
                        $registration === '' ? null : $registration
                    );
                    echo $ok ? "Student updated.\n" : "No student data changed.\n";
                    break;

                case '6':
                    $id = inputInt("Student ID to delete: ");
                    if ($id === null) { echo "Invalid student ID.\n"; break; }
                    echo $student->deleteStudent($id)
                        ? "Student deleted.\n"
                        : "Student not found or cannot be deleted because related records exist.\n";
                    break;

                case '7':
                    $reg = input("Registration number: ");
                    $record = $student->getStudentByRegistration($reg);
                    $record ? showData([$record]) : print("Student not found.\n");
                    break;

                case '8':
                    $courseId = inputInt("Course ID: ");
                    if ($courseId === null) { echo "Invalid course ID.\n"; break; }
                    showData($student->getStudentsByCourse($courseId));
                    break;

                case '9':
                    showData($student->getActiveStudents());
                    break;

                default:
                    echo "Invalid option.\n";
            }
        } catch (Throwable $e) {
            echo "Student operation failed: " . $e->getMessage() . "\n";
        }
    }
}

/* ------------------------- COURSE SECTION ------------------------- */

function courseMenu(Course $course): void
{
    while (true) {
        $choice = showMenu("COURSE MANAGEMENT", [
            1 => "Display all courses",
            2 => "Add course",
            3 => "Find course by ID",
            4 => "Search course by name",
            5 => "Update course",
            6 => "Delete course",
            7 => "Search course by code",
            8 => "Display active courses",
            9 => "Display students enrolled in a course",
            0 => "Back to main menu"
        ]);
        if ($choice === '0') return;

        try {
            switch ($choice) {
                case '1': showData($course->getCourse()); break;

                case '2':
                    $name = input("Course name: ");
                    $duration = input("Duration: ");
                    $code = input("Course code: ");
                    $fee = inputFloat("Course fee: ");
                    $start = input("Start date YYYY-MM-DD (blank if not set): ");
                    $end = input("End date YYYY-MM-DD (blank if not set): ");
                    $description = input("Description: ");
                    $status = input("Course status [Active/Inactive] (default Active): ");

                    if ($name === '' || $duration === '' || $code === '' || $fee === null || $fee < 0) {
                        echo "Course name, duration, code and a valid non-negative fee are required.\n";
                        break;
                    }
                    $ok = $course->addCourse(
                        $name, $duration, $code, $fee,
                        $start === '' ? null : $start,
                        $end === '' ? null : $end,
                        $description === '' ? null : $description,
                        $status === '' ? 'Active' : $status
                    );
                    echo $ok ? "Course added successfully.\n" : "Course was not added.\n";
                    break;

                case '3':
                    $id = inputInt("Course ID: ");
                    if ($id === null) { echo "Invalid course ID.\n"; break; }
                    $record = $course->getCourseById($id);
                    $record ? showData([$record]) : print("Course not found.\n");
                    break;

                case '4':
                    showData($course->searchCourse(input("Course name to search: ")));
                    break;

                case '5':
                    $id = inputInt("Course ID to update: ");
                    if ($id === null || !$course->getCourseById($id)) {
                        echo "Course not found or invalid ID.\n";
                        break;
                    }
                    $name = input("New course name: ");
                    $duration = input("New duration: ");
                    $code = input("New course code: ");
                    $fee = inputFloat("New course fee: ");
                    $start = input("New start date YYYY-MM-DD (blank if not set): ");
                    $end = input("New end date YYYY-MM-DD (blank if not set): ");
                    $description = input("New description: ");
                    $status = input("New course status [Active/Inactive]: ");

                    if ($name === '' || $duration === '' || $code === '' || $fee === null || $fee < 0) {
                        echo "Enter valid course details.\n";
                        break;
                    }
                    $ok = $course->updateCourse(
                        $id, $name, $duration, $code, $fee,
                        $start === '' ? null : $start,
                        $end === '' ? null : $end,
                        $description === '' ? null : $description,
                        $status === '' ? 'Active' : $status
                    );
                    echo $ok ? "Course updated.\n" : "No course data changed.\n";
                    break;

                case '6':
                    $id = inputInt("Course ID to delete: ");
                    if ($id === null) { echo "Invalid course ID.\n"; break; }
                    echo $course->deleteCourse($id)
                        ? "Course deleted.\n"
                        : "Course not found or students are still assigned to it.\n";
                    break;

                case '7':
                    $record = $course->getCourseByCode(input("Course code: "));
                    $record ? showData([$record]) : print("Course not found.\n");
                    break;

                case '8': showData($course->getActiveCourses()); break;

                case '9':
                    $id = inputInt("Course ID: ");
                    if ($id === null) { echo "Invalid course ID.\n"; break; }
                    showData($course->getCourseStudents($id));
                    break;

                default: echo "Invalid option.\n";
            }
        } catch (Throwable $e) {
            echo "Course operation failed: " . $e->getMessage() . "\n";
        }
    }
}

/* ------------------------- DEPARTMENT SECTION ------------------------- */

function departmentMenu(Department $department): void
{
    while (true) {
        $choice = showMenu("DEPARTMENT MANAGEMENT", [
            1 => "Display all departments",
            2 => "Add department",
            3 => "Find department by ID",
            4 => "Search department by name",
            5 => "Update department",
            6 => "Delete department",
            7 => "Find department by code",
            8 => "Count departments",
            0 => "Back to main menu"
        ]);
        if ($choice === '0') return;

        try {
            switch ($choice) {
                case '1': showData($department->getDepartments()); break;
                case '2':
                    $name = input("Department name: ");
                    $code = input("Department code: ");
                    if ($name === '' || $code === '') { echo "Name and code are required.\n"; break; }
                    echo $department->addDepartment($name, $code) ? "Department added.\n" : "Department not added.\n";
                    break;
                case '3':
                    $id = inputInt("Department ID: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    $row = $department->getDepartmentById($id);
                    $row ? showData([$row]) : print("Department not found.\n");
                    break;
                case '4': showData($department->searchDepartments(input("Department name: "))); break;
                case '5':
                    $id = inputInt("Department ID to update: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    $name = input("New department name: ");
                    $code = input("New department code: ");
                    if ($name === '' || $code === '') { echo "Name and code are required.\n"; break; }
                    echo $department->updateDepartment($id, $name, $code) ? "Department updated.\n" : "No department updated.\n";
                    break;
                case '6':
                    $id = inputInt("Department ID to delete: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    echo $department->deleteDepartment($id) ? "Department deleted.\n" : "Department not found.\n";
                    break;
                case '7':
                    $row = $department->getDepartmentByCode(input("Department code: "));
                    $row ? showData([$row]) : print("Department not found.\n");
                    break;
                case '8': echo "Total departments: " . $department->getTotalDepartments() . "\n"; break;
                default: echo "Invalid option.\n";
            }
        } catch (Throwable $e) {
            echo "Department operation failed: " . $e->getMessage() . "\n";
        }
    }
}

/* ------------------------- ATTENDANCE SECTION ------------------------- */

function attendanceMenu(Attendance $attendance): void
{
    while (true) {
        $choice = showMenu("ATTENDANCE MANAGEMENT", [
            1 => "Display all attendance records",
            2 => "Mark attendance",
            3 => "Find attendance by ID",
            4 => "View attendance by student",
            5 => "Update attendance",
            6 => "Delete attendance",
            7 => "View attendance by date",
            8 => "View student attendance percentage",
            9 => "View student attendance summary",
            10 => "Count attendance records",
            0 => "Back to main menu"
        ]);
        if ($choice === '0') return;

        try {
            switch ($choice) {
                case '1': showData($attendance->getAttendance()); break;
                case '2':
                    $sid = inputInt("Student ID: ");
                    if ($sid === null) { echo "Invalid student ID.\n"; break; }
                    $date = input("Attendance date YYYY-MM-DD (blank for today): ");
                    $status = input("Status [Present/Absent/Late]: ");
                    echo $attendance->addAttendance($sid, $date === '' ? null : $date, $status)
                        ? "Attendance marked.\n" : "Attendance not marked.\n";
                    break;
                case '3':
                    $id = inputInt("Attendance ID: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    $row = $attendance->getAttendanceById($id);
                    $row ? showData([$row]) : print("Attendance record not found.\n");
                    break;
                case '4':
                    $sid = inputInt("Student ID: ");
                    if ($sid === null) { echo "Invalid student ID.\n"; break; }
                    showData($attendance->getAttendanceByStudent($sid));
                    break;
                case '5':
                    $id = inputInt("Attendance ID to update: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    $date = input("New date YYYY-MM-DD (blank to keep existing): ");
                    $status = input("New status [Present/Absent/Late]: ");
                    echo $attendance->updateAttendance($id, $date === '' ? null : $date, $status)
                        ? "Attendance updated.\n" : "No attendance record updated.\n";
                    break;
                case '6':
                    $id = inputInt("Attendance ID to delete: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    echo $attendance->deleteAttendance($id) ? "Attendance deleted.\n" : "Record not found.\n";
                    break;
                case '7':
                    $date = input("Date YYYY-MM-DD: ");
                    showData($attendance->getAttendanceByDate($date));
                    break;
                case '8':
                    $sid = inputInt("Student ID: ");
                    if ($sid === null) { echo "Invalid student ID.\n"; break; }
                    echo "Attendance percentage: " . $attendance->getAttendancePercentage($sid) . "%\n";
                    break;
                case '9':
                    $sid = inputInt("Student ID: ");
                    if ($sid === null) { echo "Invalid student ID.\n"; break; }
                    showData([$attendance->getAttendanceSummary($sid)]);
                    break;
                case '10': echo "Total attendance records: " . $attendance->getTotalAttendance() . "\n"; break;
                default: echo "Invalid option.\n";
            }
        } catch (Throwable $e) {
            echo "Attendance operation failed: " . $e->getMessage() . "\n";
        }
    }
}

/* ------------------------- MARKS SECTION ------------------------- */

function marksMenu(Marks $marks): void
{
    while (true) {
        $choice = showMenu("MARKS MANAGEMENT", [
            1 => "Display all marks",
            2 => "Add marks",
            3 => "Find mark record by ID",
            4 => "View marks by student",
            5 => "Update marks",
            6 => "Delete marks",
            7 => "Calculate student percentage",
            8 => "Display student total marks",
            9 => "Calculate student grade",
            10 => "Search marks by subject",
            11 => "Count mark records",
            0 => "Back to main menu"
        ]);
        if ($choice === '0') return;

        try {
            switch ($choice) {
                case '1': showData($marks->getMarks()); break;
                case '2':
                    $sid = inputInt("Student ID: ");
                    $subject = input("Subject name: ");
                    $exam = input("Exam type: ");
                    $total = inputInt("Total marks (positive integer): ");
                    $obtainedRaw = input("Obtained marks: ");
                    $obtained = filter_var($obtainedRaw, FILTER_VALIDATE_INT);
                    if ($sid === null || $subject === '' || $exam === '' || $total === null || $obtained === false) {
                        echo "Enter valid mark details.\n"; break;
                    }
                    echo $marks->addMarks($sid, $subject, $exam, $total, $obtained)
                        ? "Marks added.\n" : "Marks not added.\n";
                    break;
                case '3':
                    $id = inputInt("Mark ID: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    $row = $marks->getMarksById($id);
                    $row ? showData([$row]) : print("Mark record not found.\n");
                    break;
                case '4':
                    $sid = inputInt("Student ID: ");
                    if ($sid === null) { echo "Invalid student ID.\n"; break; }
                    showData($marks->getMarksByStudent($sid));
                    break;
                case '5':
                    $id = inputInt("Mark ID to update: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    $subject = input("New subject name: ");
                    $exam = input("New exam type: ");
                    $total = inputInt("New total marks: ");
                    $obtained = filter_var(input("New obtained marks: "), FILTER_VALIDATE_INT);
                    if ($subject === '' || $exam === '' || $total === null || $obtained === false) {
                        echo "Enter valid mark details.\n"; break;
                    }
                    echo $marks->updateMarks($id, $subject, $exam, $total, $obtained)
                        ? "Marks updated.\n" : "No mark record updated.\n";
                    break;
                case '6':
                    $id = inputInt("Mark ID to delete: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    echo $marks->deleteMarks($id) ? "Marks deleted.\n" : "Mark record not found.\n";
                    break;
                case '7':
                    $sid = inputInt("Student ID: ");
                    if ($sid === null) { echo "Invalid student ID.\n"; break; }
                    echo "Percentage: " . $marks->getStudentPercentage($sid) . "%\n";
                    break;
                case '8':
                    $sid = inputInt("Student ID: ");
                    if ($sid === null) { echo "Invalid student ID.\n"; break; }
                    showData([$marks->getStudentTotalMarks($sid)]);
                    break;
                case '9':
                    $sid = inputInt("Student ID: ");
                    if ($sid === null) { echo "Invalid student ID.\n"; break; }
                    echo "Grade: " . $marks->getStudentGrade($sid) . "\n";
                    break;
                case '10': showData($marks->getSubjectMarks(input("Subject to search: "))); break;
                case '11': echo "Total mark records: " . $marks->getTotalMarkRecords() . "\n"; break;
                default: echo "Invalid option.\n";
            }
        } catch (Throwable $e) {
            echo "Marks operation failed: " . $e->getMessage() . "\n";
        }
    }
}

/* ------------------------- FEES SECTION ------------------------- */

function feesMenu(Fees $fees): void
{
    while (true) {
        $choice = showMenu("FEES MANAGEMENT", [
            1 => "Display all fee records",
            2 => "Add fee record",
            3 => "Find fee by ID",
            4 => "View fees by student",
            5 => "Update fee record",
            6 => "Record an additional payment",
            7 => "Display pending fees",
            8 => "Delete fee record",
            9 => "Display total fees",
            10 => "Display total paid amount",
            11 => "Display total pending amount",
            0 => "Back to main menu"
        ]);
        if ($choice === '0') return;

        try {
            switch ($choice) {
                case '1': showData($fees->getFees()); break;
                case '2':
                    $sid = inputInt("Student ID: ");
                    $total = inputFloat("Total fee: ");
                    $paid = inputFloat("Initial paid amount: ");
                    $date = input("Payment date YYYY-MM-DD (blank for today): ");
                    if ($sid === null || $total === null || $paid === null) {
                        echo "Enter valid student ID and fee amounts.\n"; break;
                    }
                    echo $fees->addFee($sid, $total, $paid, $date === '' ? null : $date)
                        ? "Fee record added.\n" : "Fee record not added.\n";
                    break;
                case '3':
                    $id = inputInt("Fee ID: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    $row = $fees->getFeeById($id);
                    $row ? showData([$row]) : print("Fee record not found.\n");
                    break;
                case '4':
                    $sid = inputInt("Student ID: ");
                    if ($sid === null) { echo "Invalid student ID.\n"; break; }
                    showData($fees->getFeesByStudent($sid));
                    break;
                case '5':
                    $id = inputInt("Fee ID to update: ");
                    $total = inputFloat("New total fee: ");
                    $paid = inputFloat("New paid amount: ");
                    $date = input("Payment date YYYY-MM-DD (blank to keep existing): ");
                    if ($id === null || $total === null || $paid === null) {
                        echo "Enter valid fee details.\n"; break;
                    }
                    echo $fees->updateFee($id, $total, $paid, $date === '' ? null : $date)
                        ? "Fee record updated.\n" : "No fee record updated.\n";
                    break;
                case '6':
                    $id = inputInt("Fee ID: ");
                    $amount = inputFloat("Additional payment amount: ");
                    $date = input("Payment date YYYY-MM-DD (blank for today): ");
                    if ($id === null || $amount === null) { echo "Enter valid fee ID and amount.\n"; break; }
                    echo $fees->recordPayment($id, $amount, $date === '' ? null : $date)
                        ? "Payment recorded.\n" : "Payment not recorded.\n";
                    break;
                case '7': showData($fees->getPendingFees()); break;
                case '8':
                    $id = inputInt("Fee ID to delete: ");
                    if ($id === null) { echo "Invalid ID.\n"; break; }
                    echo $fees->deleteFee($id) ? "Fee record deleted.\n" : "Fee record not found.\n";
                    break;
                case '9': echo "Total fees: " . $fees->getTotalFees() . "\n"; break;
                case '10': echo "Total paid: " . $fees->getTotalPaidAmount() . "\n"; break;
                case '11': echo "Total pending: " . $fees->getTotalPendingAmount() . "\n"; break;
                default: echo "Invalid option.\n";
            }
        } catch (Throwable $e) {
            echo "Fee operation failed: " . $e->getMessage() . "\n";
        }
    }
}

/* ------------------------- REPORTS SECTION ------------------------- */

function reportsMenu(Report $report): void
{
    while (true) {
        $choice = showMenu("REPORTS AND STATISTICS", [
            1 => "Dashboard counts",
            2 => "Student status report",
            3 => "Course-wise student count",
            4 => "Students without a course",
            5 => "Attendance summary",
            6 => "Student-wise attendance report",
            7 => "Student-wise marks report",
            8 => "Overall fee summary",
            9 => "Student-wise fee report",
            10 => "Students admitted between dates",
            11 => "Total departments",
            12 => "Total students",
            13 => "Total courses",
            0 => "Back to main menu"
        ]);
        if ($choice === '0') return;

        try {
            switch ($choice) {
                case '1': showData([$report->getDashboardCounts()]); break;
                case '2': showData($report->getStudentStatusReport()); break;
                case '3': showData($report->getCourseWiseStudentCount()); break;
                case '4': showData($report->getStudentsWithoutCourse()); break;
                case '5': showData($report->getAttendanceSummary()); break;
                case '6': showData($report->getStudentAttendanceReport()); break;
                case '7': showData($report->getStudentMarksReport()); break;
                case '8': showData([$report->getFeeSummary()]); break;
                case '9': showData($report->getStudentFeeReport()); break;
                case '10':
                    $start = input("Start date YYYY-MM-DD: ");
                    $end = input("End date YYYY-MM-DD: ");
                    showData($report->getStudentsByAdmissionDate($start, $end));
                    break;
                case '11': echo "Total departments: " . $report->getDepartmentCount() . "\n"; break;
                case '12': echo "Total students: " . $report->getTotalStudents() . "\n"; break;
                case '13': echo "Total courses: " . $report->getTotalCourses() . "\n"; break;
                default: echo "Invalid option.\n";
            }
        } catch (Throwable $e) {
            echo "Report operation failed: " . $e->getMessage() . "\n";
        }
    }
}

/* ------------------------- MAIN PROGRAM ------------------------- */

try {
    $database = new Database();
    $conn = $database->connect();

    $student = new Student($conn);
    $course = new Course($conn);
    $department = new Department($conn);
    $attendance = new Attendance($conn);
    $marks = new Marks($conn);
    $fees = new Fees($conn);
    $report = new Report($conn);

    while (true) {
        $choice = showMenu("STUDENT MANAGEMENT SYSTEM - MAIN MENU", [
            1 => "Student Management",
            2 => "Course Management",
            3 => "Department Management",
            4 => "Attendance Management",
            5 => "Marks Management",
            6 => "Fees Management",
            7 => "Reports and Statistics",
            0 => "Exit"
        ]);

        switch ($choice) {
            case '1': studentMenu($student, $course); break;
            case '2': courseMenu($course); break;
            case '3': departmentMenu($department); break;
            case '4': attendanceMenu($attendance); break;
            case '5': marksMenu($marks); break;
            case '6': feesMenu($fees); break;
            case '7': reportsMenu($report); break;
            case '0':
                echo "Thank you for using Student Management System. Goodbye!\n";
                exit;
            default:
                echo "Invalid main menu option. Please select 0 to 7.\n";
        }
    }
} catch (Throwable $e) {
    echo "Application error: " . $e->getMessage() . "\n";
}
