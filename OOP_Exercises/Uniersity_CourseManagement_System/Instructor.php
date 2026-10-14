<?php
require_once("Person.php");

class Instructor extends Person
{
    private $Specialization;

    public function __construct($id,$name,$email,$Specialization)
    {
        parent::__construct($id,$name,$email);
        $this->Specialization=$Specialization;
    }

    public function getSpecialization()
    {
        return $this->Specialization;
    }

    public function getRole()
    {
        return "Instructor";
    }

    public function displayDetails()
    {
        echo "\n";
        echo " Instructor ID: ".$this->id. "\n";
        echo " Name: ".$this->name. "\n";
        echo " Email: ".$this->email. "\n";
        echo " Specialization: ".$this->Specialization. "\n";
        echo "  Role: ".$this->getRole(). "\n";
    }
}
?>