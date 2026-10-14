<?php
class Registration
{
    private $registrationid;
    private $studentid;
    private $courseid;
    

    public function __construct($registrationid,$studentid,$courseid)
    {
        $this->registrationid=$registrationid;
        $this->studentid=$studentid;
        $this->courseid=$courseid;
      
    }

    public function getregistrationid()
    {
        return $this->registrationid;

    }

    public function getstudentid()
    {
        return $this->studentid;
    }

    public function getcourseid()
    {
        return $this->courseid;
    }

 

    public function displayregistration()
    {
        echo "\n";
        echo "Registration Id: ".$this->registrationid. "\n";
        echo "Student Id: ".$this->studentid. "\n";
        echo "Coures Id: ".$this->courseid. "\n";
        
    
    }
}
?>