<?php
class Student
{   
    public function study()
    {
        echo "Student is studying\n";
    }
}
class collegestudent extends Student
{
    public function study()
    {
        echo "college student is studying\n";
    }
}
function startStudy (Student $student)
{
    $student->study();
}
$student = new CollegeStudent();
$student1 = new Student();
startStudy($student);
startStudy($student1);
