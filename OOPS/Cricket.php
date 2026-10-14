<?php
class CricketGame
{
    private $playername;
    private $choice;
    private $playerscore=0;
    private $computerscore=0;
    private $ballsperinnings=6;


    public function __construct($playername)
    {
        $this->playername=$playername;
    }

    public function chooseRole()
    {
        echo "1.Batting\n";
        echo "2.Bowling\n";


        $input=trim(readline("\nenter your choice: "));

        while($input !== "1" && $input !== "2")
            {
                $input=trim(redaline("Enter 1 for batting or 2 for bowling: "));

            }


            $this->choice=($input === "1")? "batting" : "bowling";

            echo "\n you choose ". $this->choice. " first \n";
    }

    private function playerinnings(string $batsman, bool $playerisbatting)
    {
        $score=0;
        echo "\n----$batsman is batting----\n";

        for($ball =1; $ball <=$this->ballsperinnings; $ball++)
            {
                echo "\n Ball $ball\n";

                if($playerisbatting)
                    {
                        do{
                            $runs= (int) trim(readline("choose runs(1-6):"));
                        }while($runs < 1 || $runs > 6);

                        $computerball=random_int(1,6);


                        echo "Computer bowled: $computerball\n";

                        if($runs === $computerball)
                            {
                                echo "Out! you are dismissed.\n";
                                break;
                            }

                            $score += $runs;

                            echo "you Scored $runs runs.\n";
                    }
                    else{
                        $runs=random_int(1,6);

                        do{
                            $playerball=(int)trim(readline("choose your bowl (1,6):"));
                        }while($playerball < 1 || $playerball >6);

                        echo "Computer scored Choice: $runs\n";


                        if($runs === $playerball)
                            {
                                echo "OUT! Computer is dismissed.\n";
                                break;
                            }

                            $score += $runs;

                            echo "computer scored $runs runs.\n";
                    }

                    echo "current innings score: $score\n";
            }

            echo "battsman final score: $score\n";

            return $score;
    }
    

    public function startgame()
    {
        $this->chooseRole();

        if($this->choice === "batting")
            {
                $this->playerscore= $this->playerinnings($this->playername,true);
                $this->computerscore= $this->playerinnings("Computer",false);
            }
        else{
            $this->computerscore=$this->playerinnings("Computer",false);
            $this->playerscore=$this->playerinnings($this->playername,true);
        }

        $this->showResult();
    }

    public function showResult()
    {

        echo "========Match Result========\n";
        echo $this->playername.": ". $this->playerscore." Runs \n";
        echo "Computer: ". $this->computerscore. " Runs \n";

        if($this->playerscore > $this->computerscore)
            {
                echo "Congratulations! you win\n";
            }
        elseif($this->computerscore > $this->playerscore)
            {
                echo "Computer Wins!\n";
                echo "Better luck next time\n";

            }
        else{
            echo "The match is a tie!\n";

        }
    }
}

$name= readline("enter your name");

$game=new CricketGame($name);

if($name == "")
    {
        $name="player";
    }

$game->startgame();



?>