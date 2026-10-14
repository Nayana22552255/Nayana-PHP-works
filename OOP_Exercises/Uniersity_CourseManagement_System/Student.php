<?php
require_once("Person.php");

class Student extends Person
{
    private $department;


    public function __construct($id, $name, $email, $department)
    {
        parent::__construct($id,$name,$email);                      //parent class person
        $this->department=$department;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function getRole()
    {
        return "Student";
    }

    public function displayDetails()
    {
        echo " Student ID: ".$this->id. "\n";
        echo " Name: ".$this->name. "\n";
        echo " Email: ".$this->email. "\n";
        echo " Department: ".$this->department. "\n";
        echo "  Role: ".$this->getRole(). "\n";
    }


}

?>