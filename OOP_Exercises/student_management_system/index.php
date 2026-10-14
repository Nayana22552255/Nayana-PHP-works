<?php
require_once("student_management.php");
require_once("function.php");

$student= new Student(001,"nayana",20,"computer science",80);
$student->displaystudent();
$student->checkresult();
echo "\n";
$student1= new Student(002,"anaswara",15,"computer science",80);
$student1->displaystudent();
$student1->checkresult();

?>