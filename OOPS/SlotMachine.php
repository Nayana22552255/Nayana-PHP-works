<?php
class SlotMachine
{
    private $symbols =  ["🍒", "🍋", "🔔", "⭐"];
    private $score =0;

    public function spin()
    {
        $result=[];
        for($i=0; $i < 3; $i++)
            {
                $index=array_rand($this->symbols);
                $result[]=$this->symbols[$index];
            }

        return $result;
    }

    public function checkwinner($result)
    {
        if($result[0] == $result[1] &&
            $result[1] == $result[2])
            {
                        return 50;
            }
        if($result[0] == $result[1] ||
            $result[1] == $result[2] ||
            $result[0] == $result[2])
            {
                    return 20;
            }
        return 0;
    }
}
class Player
{
    private $name;
    private $balance;
    private $score=0;

    public function __construct($name)
    {
        $this->name=$name;
        $this->balance=100;
    }
    public function getname()
    {
        return $this->name;
    }
    public function getbalance()
    {
        return $this->balance;
    }
    public function getscore()
    {
        return $this->score;
    }

    public function payforspin()
    {
        if($this->balance >= 10)
            {
                $this->balance -= 10;
                return true;
            }

            return false;
    }

    public function addwinnings($points)
    {
        $this->balance += $points;
        $this->score += $points;
    }
}

class game{
    private $player;
    private $machine;

    public function __construct($player, $machine)
    {
        $this->player= $player;
        $this->machine=$machine;
    }

    public function strat()
    {
        echo "\n======================\n";
        echo "     Slot Machine       \n";
        echo "==========================\n";

        echo "welcome to slote machine ". $this->player->getname(), "!\n";

        do{
            echo "\nBalance: ".$this->player-> getbalance(). "points \n";
            echo "\nTotal points earned: ".$this->player->getscore(). "\n";

            if($this->player->getbalance()<10)
                {
                    echo "\n not enough points spin\n";
                    break;
                }
            $choice = strtolower(trim(readline("Spin ? (yes/no):")));
            if($choice != "yes")
                {
                    break;
                }
            $this->player->payforspin();
            $result=$this->machine->spin();
            echo "\nSpinning..\n";
            echo implode("|",$result), "\n";                //used to convert an aray into  a single string

            $prize=$this->machine->checkwinner($result);

            if($prize > 0)
                {
                    echo "You won! $prize points\n";
                    $this->player->addwinnings($prize);
                }
                else{
                    echo "no match. Better luck next time\n";
                }
        }while(true);
        echo "\n=====================================\n";
        echo "      Gmae over    \n";
        echo "=======================================\n";
        echo "Players: ".$this->player->getname(). "\n";
        echo "Total Points: ".$this->player->getscore(). "\n";
        echo "final Balance: ".$this->player->getbalance(). "\n";
        echo "Thank you for playing!\n";

    }
}
$name=trim(readline("enter your name"));
if($name == "")
    {
        $name ="player";
    }
$player = new Player($name);
$machine = new SlotMachine();


$game= new game($player, $machine);
$game->strat();
   
?>