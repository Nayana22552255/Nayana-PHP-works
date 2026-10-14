<?php
class rockpaperscissor
{
    private $choice=["Rock","Paper","Scissor"];


    public function diplaychoices()
    {
        echo "--------Choose one choices------\n";
        echo "1.Rock\n";
        echo "2.Paper\n";
        echo "3.Scissor\n";
        echo "4.Exit\n";
    }

    public function getplayerchoice($choice)
    {
        if($choice == 1){
            return "Rock";
        }elseif($choice == 2){
            return "Paper";
        }elseif($choice == 3){
            return "Scissor";
        }
        else{
             return null;
        }

    }
    public function getcomputerchoice()
    {
        $randomindex=rand(0,2);
        return $this->choice[$randomindex];
    }

    public function determinewinner($player,$computer)
    {
            if ($player == $computer){
                return "Draw";
            }
           if(($player == "Rock" && $computer == "Scissors")||
                ($player == "Paper" && $computer == "Rock")||
                ($player == "Scissors" && $computer == "Paper"))
            {
                return "You win";
            }
    
                return  "computer wins";
            
    }
    public function play()
    {
        while(true){
                $this->diplaychoices();

                $choice= readline("enter your choice");

                if($choice == 4){
                    echo "Thank you for playing\n";
                    break;
                }
                    
                    $playerchoice = $this->getplayerchoice($choice);

                    if($playerchoice == null){
                             echo "Invalid choice.please select 1,2,3, or 4 \n";
                        continue;
                    }
                    $computerchoice=$this->getcomputerchoice();

                    $result=$this->determinewinner($playerchoice,$computerchoice);


                    echo"\n-----------------\n";
                    echo "your choice :".$playerchoice."\n";
                    echo "Computer choice :".$computerchoice."\n";
                    echo "Result :".$result."\n";

        }
    }

}
$game= new rockpaperscissor();
$game->play();

?>