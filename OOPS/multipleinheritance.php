<?php

interface Study 
{
    public function study();
}

interface Play
{
    public function play();
}

class Student implements Study, Play
{
    public function study()
    {
        echo "Student is studying\n";
    }

    public function play()
    {
        echo "Student is playing\n";
    }
}

$student = new Student();

$student->study();
$student->play();

?>


