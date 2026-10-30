<?php

class Student
{
    private $pdo;

    public function __construct()
    {
        $host="localhost";
        $dbname="student_db";
        $username="root";
        $password="root";


        try
        {

            $this->pdo=new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password
            );

            $this->pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        }catch(PDOException $e)
        {
            die("connection failed:". $e->getMessage());
        }
    }

    public function addStudent($name,$email,$phone,$age,$course)
    {
        $sql="INSERT INTO students (name,email,phone,age,course)
                           VALUES(:name,:email,:phone,:age,:course)";

        $stmt=$this->pdo->prepare($sql);

        $stmt->execute([
            ':name'=>$name,
            ':email'=>$email,
            ':phone'=>$phone,
            ':age'=>$age,
            ':course'=>$course
        ]);

        echo "Students added successfully!";
    }

    public function displayStudents()
    {
        $sql="SELECT * FROM students";
        $stmt=$this->pdo->query($sql);

        $students=$stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($students as $student)
            {
                echo "-------------------------------\n";
                echo "ID: ".$student['id']. "\n";
                echo "Name: ".$student['name']. "\n";
                echo "Email: ".$student['email']. "\n";
                echo "Phone: ".$student['phone']. "\n";
                echo "Age: ".$student['age']. "\n";
                echo "Course: ".$student['course']. "\n";
                echo "-------------------------------\n";
            }
    }
}

$student= new Student();

while (true) {
    echo "\n1. Add student\n";
    echo "2. Display students\n";
    echo "3. Exit\n";

    $choice = readline("Enter your choice: ");

    switch ($choice) {
        case '1':
            $name = readline("Enter the name: ");
            $email = readline("Enter the email: ");
            $phone = readline("Enter the phone number: ");
            $age = readline("Enter the age: ");
            $course = readline("Enter the course: ");
            $student->addStudent($name, $email, $phone, $age, $course);
            break;

        case '2':
            $student->displayStudents();
            break;

        case '3':
            exit;

        default:
            echo "Invalid choice. Please enter 1, 2, or 3.\n";
    }
}
?>