<?php
function sum($a,$b)
{
    return $a+$b;
}
echo"sum of two numbers is ".sum(2,5)."\n";


//passing by value
function add($x,$y)
{
    $x=$x+$y;
    print"sum of two numbers $x"."\n";

}
add(5,3)

//passing by reference
/*function product(&$x,&$y)
{
    return $x*$y;
}
$a=10;
$b=5;
echo"product of two numbers ".product($a,$b)."\n";
//echo $a."\n"; */
?>

