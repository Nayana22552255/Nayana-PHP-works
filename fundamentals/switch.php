<?php
$x=readline("enter the first number: ");
$y=readline("enter the second number: ");
$op=readline("enter the operator: ");
switch($op){
    case "+":
        echo"addition of two numbers is: $x + $y = " . ($x + $y)."\n" ;
        break;
    case "-":
        echo"difference of two numbers is: $x - $y = " . ($x - $y)."\n" ;
        break;
    case "*":
        echo"product of two numbers is: $x * $y = " . ($x * $y)."\n" ;
        break;
    case "/":
        echo"quotient of two numbers is: $x / $y = " . ($x / $y)."\n" ;
        break;
    case "**":
        echo"exponent of $x to the power of $y = " . ($x ** $y)."\n" ;
        break;
    case "%":
        echo"remainder of $x % $y = " . ($x % $y)."\n" ;
        break;
    default:
        echo"invalid input";
}