
<?php

class Employee
{
    private $id;
    private $name;
    private $department;
    private $basicSalary;
    private $allowance;
    private $deduction;

    public function __construct(
        $id,
        $name,
        $department,
        $basicSalary,
        $allowance,
        $deduction
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->department = $department;
        $this->basicSalary = $basicSalary;
        $this->allowance = $allowance;
        $this->deduction = $deduction;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function getBasicSalary()
    {
        return $this->basicSalary;
    }

    public function getAllowance()
    {
        return $this->allowance;
    }

    public function getDeduction()
    {
        return $this->deduction;
    }

    public function calculateSalary()
    {
        return $this->basicSalary
            + $this->allowance
            - $this->deduction;
    }

    public function displaySalarySlip()
    {
        echo "\n========== SALARY SLIP ==========\n";
        echo "Employee ID: " . $this->id . "\n";
        echo "Name: " . $this->name . "\n";
        echo "Department: " . $this->department . "\n";
        echo "Basic Salary: Rs. " . number_format($this->basicSalary, 2) . "\n";
        echo "Allowance: Rs. " . number_format($this->allowance, 2) . "\n";
        echo "Deduction: Rs. " . number_format($this->deduction, 2) . "\n";
        echo "---------------------------------\n";
        echo "Net Salary: Rs. " . number_format($this->calculateSalary(), 2) . "\n";
        echo "=================================\n";
    }

    public function toArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "department" => $this->department,
            "basicSalary" => $this->basicSalary,
            "allowance" => $this->allowance,
            "deduction" => $this->deduction
        ];
    }
}
