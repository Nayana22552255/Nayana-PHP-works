<?php
require_once("Employee.php");


class Manager extends Employee{
    public function calculateBonus()
    {
            return $this->getsalary() * 0.20;
            echo "\n";
    }
}
?>