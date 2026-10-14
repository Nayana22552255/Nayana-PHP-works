<?php

class Dice
{
    //private $numbers=[];

public function Roll()
{
        return rand(1,6);

}
}


class  Game
{
    private $dice;

    public function __construct()
    {
        $this->dice= new Dice();            //game can use $this->dice->Roll()
    }

    public function Play()
    {
        $player=$this->dice->Roll();
        $computer=$this->dice->Roll();

        echo "Player Rolled: $player \n";
        echo "Computer Rolled: $computer \n";

        if($player > $computer)
            {
                echo "Player Win \n";

            }
        elseif($computer > $player)
            {
                echo "Computer Win \n";
            }
        else
            {
                echo "Draw \n";

            }
    }

}

$game= new Game();
$game->Play();


?>