<?php
class Dice
{
    private int $number=1;

    public function roll(): int
    {
        $this->number= random_int(1,6);
        return $this->number;
    }

    public function getnumber():int
    {
            return $this->number;
    }
}

$dice= new Dice();

for($i=1; $i<=3; $i++)
    {
        $result=$dice->roll();

        echo "Roll $i: ".$result. "\n";
    }
?>