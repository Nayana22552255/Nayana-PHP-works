<?php
class Players
{
    private $name;                     //player name
    private $score;                    //player score
    private $attempts;                 //number of  attempts a player have    

    public function __construct($name)         //create a conctructor and pass the player name
    {
        $this->name=$name;                   //pont to the current player object  
        $this->score=0;                        //Initially set player plyer score as zero
        $this->attempts=0;                        //Initially set player attempts as zero
    }

    public function getPlayerName()             //getting the player name
    {
            return $this->name;                   //returning the player name
    }
    public function addScore($point)                      //adding the player score
    {
        return $this->score +=$point;                     //incresing the player score 
    }
    public function getPlayerScore()                //getting the palyer score
    {
            return $this->score;                   //returning the player score
    }
    public function getAttempts()                    //getting the numner of attempts
    {
            return $this->attempts;                   //returning the number of attempts
    }

    public function IncreaseAttempts()
    {
        return $this->attempts++;
    }

    /*public function resetscore()
    {
        $this->score=0;
    }

    public function resetattempts()
    {
        $this->attempts=0;
    }*/
}
?>