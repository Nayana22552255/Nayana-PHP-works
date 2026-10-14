<?php
require_once("Questions.php");
require_once("players.php");
require_once("scores.php");

class Quiz
{
    private $questions;         //store Question object
    private $player;            //store player object
    private $score;             //store score object
    private $totalquestions;       //number of questions


    public function __construct($totalquestions)
    {
        $this->totalquestions=$totalquestions;

        $this->player=new players();
        $this->score=new Score();

        $this->questions=[];
    }
    
    public function startquize()
    {
                echo "=================================\n";
                echo "        Math Quize                \n";
                echo "===================================\n";

                echo "enter your name: \n";             //get player name
                $name=trim(fgets(STDIN));               //read input enterd by user//trim()remove the unnecessary white space and newline character
                $this->player->setname($name);               //sending the name to player
                echo "Hello ".$this->player->getname(). "!". "\n";      //getting the name

                echo "You have ".$this->totalquestions. "Questions". "\n";

                echo "\n";
                $this->createquestions();

                for($i=0; $i< $this->totalquestions; $i++)
                    {
                        $this->showquestions($i);
                        $this->askquestions();
                        $this->checkanswers($i);
                        echo "\n";
                    }

                    $this->showresults();
                    $this->endquiz();
    
    
    }

    public function createquestions()
    {
            for($i=0; $i< $this->totalquestions; $i++)
                {
                    $question= new Question();              //creating a question object
                    $question->generatequestion();           //connect to the generatequestion
                    $this->questions[]=$question;           //Storing questions to question object
                  
                }

    }

      public function showquestions($index)               //show questions index by index
    {
            echo "\n";
            echo "Questions".($index+1). " :";            //increamenting the index
            echo $this->questions[$index]->getquestion();      //get the first question object and calling the getquestion()
          
    }

    public function askquestions()          //asks for the questions
    {
        echo "Your answer: ";                 //asks for the player answer
        $answer=(int) trim(fgets(STDIN));         //create the answer object for storing the name
        $this->player->setanswer($answer);      //store the answer in player
    }

    public function checkanswers($index)          //check for the answers through the index
    {
        $playeranswer=$this->player->getanswer();

        $correctanswer=$this->questions[$index]->getcorrectanswer();
             //getting the correct answer
        if($playeranswer == $correctanswer )                            //comparing the answers
        {
             echo "Correct! \n";
            
            $this->score->addpoint();
        }
        else{

                    echo "Wrong! \n";
                //echo  $correctanswer. "\n";
        }
        $this->score->displayscore($index +1);
    
    }
  


    public function showresults()           //showing the final result
    {
        echo "\n";
        echo "====================================\n";
        echo "             Quize Result            \n";
        echo "======================================\n";
        echo "player: ".$this->player->getname(). "\n";
        echo "Total Questions: ".$this->totalquestions. "\n";
        echo "Final score: ".$this->score->getscore(). "/". $this->totalquestions. "\n";
        echo "========================================\n";
    }
    public function endquiz()
    {
            echo "\n";
            echo "Thank you for Playing! ". "\n";
    }
}

?>