//method overriding
<?php
class Employee
{
    public $Ename;
    public function __construct($Ename)
    {
        $this->Ename=$Ename;
        }
        public function display()
        {
                echo $this->Ename." is the employee"."\n";
        }

    
}
class Developer extends Employee
{
    
    public function display(){
        echo $this->Ename." is the software developer"."\n";
        
}
}
$employee=new Employee("nayana");
$developer=new Developer("anu");
$employee->display();
$developer->display();