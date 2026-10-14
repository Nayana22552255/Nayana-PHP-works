<?php

require_once("Employee.php");
require_once("Manager.php");
require_once("Developer.php");



//Employee1
$employee=new Employee(101,"nayana",22, "finace",1000);
echo "\n";

//Manager
$manager=new Manager(102, "anu", 22, "Management", 1000);
echo "\n";


//Developer
$developer=new Developer(103,"gopika",24,"IT",5000);
echo "\n";


//display employee details
echo "Employee Details";
echo $employee->displayemployee();
echo "\n";

echo "Annual Salary:";
echo $employee->calculateAnnualSalary();
echo "\n";

echo "Bonus:";
echo $employee->calculateBonus();
echo "\n";

echo "Total Salary:";
echo $employee->calcualteTotalSalary();
echo "\n";
echo "\n";




//Display Manager
echo "Manager deatils ";
echo $manager-> displayemployee();
echo "\n";

echo "Annual Salary:";
echo $manager->calculateAnnualSalary();
echo "\n";

echo "Bonus";
echo $manager->calculateBonus();
echo "\n";

echo "Total Salary";
echo $manager->calcualteTotalSalary();
echo "\n";
echo "\n";

//Display Developer
echo "Developer deatils ";
echo $developer->displayemployee();
echo "\n";

echo "Annual Salary:";
echo $developer->calculateAnnualSalary();
echo "\n";

echo "Bonus";
echo $developer->calculateBonus();
echo "\n";

echo "Total Salary";
echo $developer->calcualteTotalSalary();
echo "\n";





?>