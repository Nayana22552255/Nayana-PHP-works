<?php
class MemoryRecall
{
    private $items=[];
    private $score=0;

    public function __construct()
    {
        $this->items=[
            "Apple",
            "Parrot",
            "BMW",
            "Orange",
            "Mango",
            "owl",
            "splender",
            "Banana"
        ];
        shuffle($this->items);
    }

    public function displayitems()
    {
        echo "Memory Recall Game\n";
        echo "Remeber each items and its positions\n\n";


        foreach($this->items as $index => $item)
            {
                echo "$index => $item\n";
            }
    }

    public function hideitems()
    {
        echo "The list will disappear in 5 seconds\n";
        sleep(5);

        if (PHP_OS_FAMILY === "Windows") {
            system( "powershell -Command Clear-Host");      
        } else {
            system("clear");
        }

        echo "The items are now hidden.\n";
    }

    public function play()
    {
        foreach($this->items as $index => $item)
            {
                echo "what was the item at the index $index?\n";

                $answer = trim(readline());

                //used to compare two strings without considering lowercase or uppercase
                if(strcasecmp($answer, $item) === 0)
                    {
                        echo "Correct Answer!\n";
                        $this->score++;
                    }
                else{
                    echo "wrong answer\n";
                    echo "Correct items: $item\n";
                }
            }

            $this->showresult();

    }

    public function showresult()
    {
        $total= count($this->items);

        echo "game over\n";
        echo "your score:" .$this->score. "\n";


        if($this->score === $total)
            {
                echo "Excellent! Perfect memory!\n";
            }elseif($this-> score >= $total / 2)
            {
                echo "Good Job! Keep practicing\n";
            }
            else{
                echo "Try again to improve you memory!\n";
            }
    }

}

$game= new MemoryRecall();

$game->displayitems();

$game->hideitems();

$game->play();



?>