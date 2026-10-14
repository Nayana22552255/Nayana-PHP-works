<?php

class Course
{
    private $courseid;
    private $coursename;
    private $credits;
    private $instructorid;
    private $duration;


    public function __construct($courseid, $coursename, $credits, $instructorid, $duration)
    {
                $this->courseid=$courseid;
                $this->coursename=$coursename;
                $this->credits=$credits;
                $this->instructorid=$instructorid;
                $this->duration=$duration;
    }

    public function getcourseid()
    {
        return $this->courseid;
    }

    public function getcoursename()
    {
        return $this->coursename;

    }

    public function getcredits()
    {
        return $this->credits;
    }

    public function getinstructorid()
    {
        return $this->instructorid;
    }

    public function getduration()
    {
        return $this->duration;
    }

    public function displaycousre()
    {
        echo "\n";
        echo "Course Id: ".$this->courseid. "\n";
        echo "Course Name: ".$this->coursename. "\n";
        echo "Credits: ".$this->credits. "\n";
        echo "Instructor: ".$this->instructorid. "\n";
        echo "Course Duration: ".$this->duration. "\n";
    }
}
?>