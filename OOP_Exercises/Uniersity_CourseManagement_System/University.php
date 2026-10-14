<?php
require_once("Student.php");
require_once("Instructor.php");
require_once("Course.php");
require_once("Registration.php");

class University
{
    private $students=[];
    private $insrtuctors=[];
    private $courses=[];
    private $registrations=[];

    private $file="Data.json";


    public function __construct()
    {
        $this->loadData();
    }

    private function loadData()
    {
        if(!file_exists($this->file))
            {
                echo "Data.json file not fount";
                return;
            }
        $jsonData=file_get_contents($this->file);
        $data=json_decode($jsonData,true);

        foreach($data["students"] as $student)
            {
                $this->students[]=new Student(
                    $student["id"],
                    $student["name"],
                    $student["email"],
                    $student["department"]
                );
            }

        foreach($data["instructors"] as $instructor)
            {
                $this->Instructors[]=new Instructor(
                    $instructor["id"],
                    $instructor["name"],
                    $instructor["email"],
                    $instructor["Specialization"]
                );
            }

        foreach($data["courses"] as $course)
            {
                $this->courses[]=new Course(
                    $course["courseid"],
                    $course["coursename"],
                    $course["credits"],
                    $course["instructorid"],
                    $course["duration"]
                );
            }

        foreach($data["registrations"] as $register)
            {
                $this->registrations[]=new Registration(
                    $register["registrationid"],
                    $register["studentid"],
                    $register["courseid"],
                   
                );
            }
    }


    public function displaystudents()
    {
        echo "=====Students=====\n";
        foreach($this->students as $student)
            {
                $student->displayDetails();
                echo "\n";
            }
    }

    public function displayinstructor()
    {
        echo "======Instructor=====\n";
        foreach($this->Instructors as $instructor)
            {
                $instructor->displayDetails();
            }
    }

    public function displaycousres()
    {
        echo "=====Course======\n";
        foreach($this->courses as $course)
            {
                $course->displaycousre();
            }
    }

    public function diplayregistrations()
    {
        echo "======Register====\n";
        foreach($this->registrations as $register)
            {
                $register->displayregistration();
            }
    }

    public function findstudents($id)
    {
        foreach($this->students as $student)
            {
                if($student->getId() == $id)
                    {
                        return $student;
                    }
            }
            return null;
    }

    public function findcourse($courseid)
    {
        foreach($this->Courses as $course)
            {
                if($course->getcourseid() == $courseid)
                    {
                        return $course;
                    }
            }

            return null;
    }
}
?>