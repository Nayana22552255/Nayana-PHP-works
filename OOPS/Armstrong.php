<?php

class Armstrong
{
    private $number;

    public function __construct($number)
    {
        $this->number=$number;
    }

    public function isArmstrong()
    {
        $temp=$this->number;
        $digits=strlen((string)$this->number);
        $sum=0;

        while($temp>0)
            {
                $digit= $temp % 10;
                $sum=$sum + pow($digit,$digits);
                $temp= (int)($temp /10);
            }

        if($this->number == 0)
            {
                $sum= 0;
            }
        return $sum == $this->number;
    }

    public function displayResult()
    {
        if($this->isArmstrong())
            {
                echo $this->number. "  is an armstrong number\n";
            }
            else{
                echo $this->number. "  is not an armstrong number\n";
            }
    }
}

echo "Enter a number: ";
$input=trim(readline());        //trim() removes extra white spaces

if(ctype_digit($input))         //ctype_digit check whether the input containts only digits
{
    $number=(int)$input;        //convert the input into integer

    $obj= new Armstrong($number);
    $obj->displayResult();
}
else
    {
        echo "Please enter a non-negative integer";
    }
?>