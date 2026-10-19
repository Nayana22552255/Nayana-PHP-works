<?php
class GCDClacalculate
{
    private $number1;
    private $number2;

    public function __construct($number1, $number2)
    {
        if($number1 <=0 || $number2 <=0)
            {
                throw InvalidArgumentException("enter a positive integer");
            }

            $this->number1=$number1;
            $this->number2=$number2;
    }

    public function calculate()
    {
        $number1=abs($this->number1);
        $number2=abs($this->number2);

        while($number2 != 0)
            {
                $reminder= $number1 % $number2;
                $number1=$number2;
                $number2= $reminder ;
            }

            return $number1;
    }
}

$number1=readline("enter the first number");
$number2=readline("enter the second number");

$cal=new GCDClacalculate($number1, $number2);
echo "GCD of $number1 and $number2 are: ";
echo $cal-> calculate();
?>