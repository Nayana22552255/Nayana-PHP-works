<?php
require_once("Employee.php");

class Developer extends Employee{
    public function  calculateBonus()
    {
            return $this->getsalary() * 0.15;
            echo "\n";
    }

}

?>