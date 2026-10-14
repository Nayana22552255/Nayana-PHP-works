<?php
class Student{
    public $name,$age,$rollno;
    public  function study()
    {
        echo $this->name." is studying \n";
    
        echo $this->age." is years old\n";
        echo $this->rollno." is the roll number\n";
          }
}
$student1=new Student();
$student1->name="Nayana";
$student1->age=22; 
$student1->rollno=3;
$student1->study();

?>