<?php
class checknumber
{
    public $number;

    public function __construct($number)
    {
        $this->number=$number;
    }
    public function check()
    {
        echo "checking the number\n";
    }
}

class Prime extends checknumber
{
    public function check()
    {
        if($this->number <= 1)
            {
                echo "$number is not a prime number\n";
                return;
            }
        for($i=2; $i <= sqrt($this->number); $i++)
            {
                if($this->number %  $i==0)
                    {
                            echo $this->number ."  is not a prime number\n";
                            return;
                    }
            }
            echo $this->number. "  is a prime number\n";
    }
}

$number=readline("enter a number");

$prime= new Prime($number);
$prime->check();
?>