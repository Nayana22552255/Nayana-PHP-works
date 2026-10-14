<?php
class Employee
{
    private $name;
    private $id;
    private $department;
    private $salary;
 
    public function __construct($name,$id,$department,$salary)
    {
        $this ->name=$name;
        $this ->id=$id;
        $this ->department=$department;
        $this ->salary=$salary;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
    return $this ->id; 
    }

    public function getDepartment()
    {
        return $this -> department;
    }

    public function getSalary()
    {
        return $this ->salary;
    }
}

$employee= new Employee("Nayana",101,"IT",10000);
echo $employee->getName();
echo "\n";
echo $employee->getId();
echo "\n";
echo $employee->getDepartment();
echo "\n";
echo $employee->getSalary();
?>
