<?php
trait Study
{
    public function study()
    {
        echo "Student is studying\n";
    }
}

trait Play
{
    public function play()
    {
        echo "Student is playing\n";
    }
}

class Student
{
    use Study, Play;
}

$student = new Student();

$student->study();
$student->play();

?>