<?php
//final keyword
class student{
    public $name;
    //public $rollno;
    public function __construct($name/*,$rollno*/)
    {
        $this->name=$name;
       // $this->rollno=$rollno;
    }
    final public function display()
    {
        echo "name of the student ".$this->name."\n";
       // echo "roll number of the given student".$rollno."\n";
    }
}
class collegestudent extends student
{
    /*public function display()
    {
        echo $this->name ."is a colleg student"."\n";   //this section ceate error like  Cannot override final method student::display() 

        //echo $this->rollno ."is the college roll number"."\n";
    }*/
}

$Collegestudent = new collegestudent("nayana"/*,3*/);
$Collegestudent->display();
?>