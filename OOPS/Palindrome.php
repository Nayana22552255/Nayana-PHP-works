<?php
interface  checker
{
    public function check();
}

class Palindrom implements checker
{
    private $value;

    public function __construct($value)
    {
        $this->value=$value;
    }

    public function check()
    {
        $original=(string) $this->value;
        $reverse=strrev($original);

        if($original == $reverse)
            {
                echo "$original  is a palindrom \n";
            }
        else{
            echo "$original  is not a palindrom \n";
        }
    }
}
$original=readline("enter a string");
$drom=new Palindrom($original);
$drom1=new Palindrom("malayalam");
$drom->check();
$drom1->check();

?>