<?php
class Student{
    public $studentID;
    public $name;
    public $rollno;
    public $course;
    public $marks;


    public function __construct($studentID,$name,$rollno,$course,$marks){
        $this->name=$name;
        $this->rollno=$rollno;
        $this->course=$course;
        $this->marks=$marks;
        $this->studentID=$studentID;
    }
    public function displaystudent()
    {
    echo "StudentID : ".$this->studentID."\n";
    echo "name: ".$this->name."\n";
    echo "rollnumber: ".$this->rollno."\n";
    echo "course : ".$this->course."\n";
    echo "mark : ".$this->marks."\n";
        


    }
     public function checkresult()
    {
        if($this->marks >= 60){
            echo "result:pass \n";
        }else{
            echo "result:fail \n";
        }
    }
   
}


?>