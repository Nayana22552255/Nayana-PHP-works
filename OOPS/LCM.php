<?php

class LCMCalculator
{
    private $num1;
    private $num2;

    public function __construct($num1, $num2)
    {
        if($num1 <=0 || $num2 <=0)
            {
                throw new InvalidArgumentException("please enter a positive integers");
            }

            $this->num1=$num1;
            $this->num2=$num2;
    }

    public function calculate()
    {
        $lcm=max($this->num1,$this->num2);
        while( $lcm % $this->num1 !=0 ||
                $lcm % $this->num2!=0)
            {
                $lcm++;
            }

            return $lcm;
     }
}

$num1=readline("enter the first number");
$num2=readline("enter the second number");

$calculator=new LCMCalculator($num1, $num2);
echo "Lcm of $num1 and $num2 are: ";

echo $calculator->calculate();
?>