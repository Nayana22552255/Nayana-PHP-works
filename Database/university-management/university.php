
<?php
require_once "db.php";

class University
{
    private PDO $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->connect();
    }

    public function addStudent(): void
    {
        $name = trim(readline("Enter student name: "));
        $age = (int) readline("Enter student age: ");

        echo "\nAvailable Departments:\n";

        $stmt = $this->pdo->query(
            "SELECT department_id, department_name FROM departments"
        );

        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($departments as $dept) {
            echo $dept["department_id"] . ". "
                . $dept["department_name"] . "\n";
        }

        $departmentId = (int) readline("Enter department ID: ");

        if ($name === "" || $age <= 0) {
            echo "Invalid student details.\n";
            return;
        }

        $check = $this->pdo->prepare(
            "SELECT department_id FROM departments
             WHERE department_id = ?"
        );
        $check->execute([$departmentId]);

        if (!$check->fetch()) {
            echo "Invalid department ID.\n";
            return;
        }

        $sql = "INSERT INTO students
                (student_name, age, department_id)
                VALUES (?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name, $age, $departmentId]);

        echo "Student added successfully!\n";
    }

    public function addCourse(): void
    {
        $courseName = trim(readline("Enter course name: "));

        echo "\nAvailable Departments:\n";

        $stmt = $this->pdo->query(
            "SELECT department_id, department_name FROM departments"
        );

        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($departments as $dept) {
            echo $dept["department_id"] . ". "
                . $dept["department_name"] . "\n";
        }

        $departmentId = (int) readline("Enter department ID: ");

        if ($courseName === "") {
            echo "Course name cannot be empty.\n";
            return;
        }

        $check = $this->pdo->prepare(
            "SELECT department_id FROM departments
             WHERE department_id = ?"
        );
        $check->execute([$departmentId]);

        if (!$check->fetch()) {
            echo "Invalid department ID.\n";
            return;
        }

        $sql = "INSERT INTO courses
                (course_name, department_id)
                VALUES (?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$courseName, $departmentId]);

        echo "Course added successfully!\n";
    }
}

