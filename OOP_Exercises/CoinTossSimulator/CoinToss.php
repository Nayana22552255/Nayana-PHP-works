<?php 

class Coin
{

    private $result;
    private $heads=0;
    private $tails=0;


    public function Toss()
    {
        $numbers=rand(0,1);

        if($numbers == 0)
            {
                $this->result="Heads";
                $this->heads++;
            }
            else{
                $this->result="Tails";
                $this->tails++;
            }
    }


    public function getResult()
    {
        return $this->result;
    }


    public function getHeadsCount()
    {
        return $this->heads;
    }
    public function getTailcount()
    {
            return $this->tails;
    }
}

$coin =new Coin();

for($i = 1; $i <= 10; $i++)
    {
        $coin->Toss(). "\n";

        echo "Rsult: ".$coin->getResult(). "\n";
    }

echo "Total Heads: ".$coin->getHeadsCount(). "\n";
echo "Total Tails: ".$coin->getTailcount(). "\n";

$total=$coin->getHeadsCount() + $coin->getTailcount();
echo "Total Toss: ".$total. "\n";