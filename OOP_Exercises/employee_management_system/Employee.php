<?php

class Employee{
    // private properties - Enacapsulation
    private $employeeId;
    private $name;
    private $age;
    private $department;
    private $salary;


    public function __construct($employeeId,$name,$age,$department,$salary)
    {
        $this->employeeId=$employeeId;
        $this->name=$name;
        $this->age=$age;
        $this->department=$department;
        $this->salary=$salary;
      }

        public function getEmployeeId()     //getter for  employee Id
        {
           return $this->employeeId;
        }

        public function setEmployeeId($employeeId)     //setter for  employee Id
        {
           return $this->employeeId;
        }


        public function getName()     //getter for  name
        {
           return $this->name;
        }

        public function setName($name){     //setter for name
              $this->name =$name;
        }


        public function getAge(){
              return $this->age;
        }

        public function setAge($age){
                $this->age=$age;
        }
         public function getDepartment(){
           return $this->department;
         }

         public function setDepartment($department){
                $this->department=$department;
         }

        public function getsalary()
        {
          return $this->salary;
        }

        public function setsalary($salary){
          
                  $this->salary= $salary;
          
        }

        public function displayemployee()
        {
        echo "\n";
        echo "Employee ID: " . $this->employeeId . "\n";
        echo "Name: " . $this->name . "\n";
        echo "Age: " . $this->age . "\n";
        echo "Department: " . $this->department . "\n";
        echo "Monthly Salary: ₹" . $this->salary . "\n";

        }


        public function calculateAnnualSalary(){
            return $this->salary * 12;
            echo "\n";
        }

        public function calculateBonus()
        {
          return $this->salary * 0.10;
          echo "\n";

        }


        public function calcualteTotalSalary()
        {
          return $this->salary + $this->calculateBonus();
          echo "\n";
        }
}
?>