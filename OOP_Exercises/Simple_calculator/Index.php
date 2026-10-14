<?php
require_once("Calculator.php");

$number1=readline("enter first number");
$number2=readline("enter the second number");
    echo "\n===== CALCULATOR =====\n";
    echo "1. Addition\n";
    echo "2. Substraction\n";
    echo "3. Multiplication\n";
    echo "4. Division\n";
    echo "5. Exit\n";


$choice=readline("enter your choice");

switch($choice)
{

case 1:
        $calculate=new Calculator($number1,$number2);
        echo "Sum: ".$calculate->Addition()."\n";
        break;

case 2:

        $calculate=new Calculator($number1,$number2);
        echo "Difference: ".$calculate->Substraction()."\n";
        break;

case 3:

        $calculate=new Calculator($number1,$number2);
        echo "Product: ".$calculate->Multiplication()."\n";
        break;

case 4:

        $calculate=new Calculator($number1,$number2);
        echo "Division: ".$calculate->Division()."\n";
        break;
case 5:
        echo "exit";
        break;        
}