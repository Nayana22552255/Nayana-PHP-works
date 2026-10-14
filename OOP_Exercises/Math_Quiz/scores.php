<?php
class Score
{
    private $score=0;                  //by initially declaire score=0
    public function addpoint()      //add one point
    {
            $this->score++;
    }
    public function getscore()      //get the current score
    {
            return $this->score;
    }
    public function displayscore($questionnumber)
    {
            echo "Score: ".$this->score. "/". $questionnumber. "\n";         //end of the line
    }
}
?>