<?php
class Game
{
    private $randomNumber;
    public function __construct()
    {
        $this->randomNumber=rand(1,100);
    }

    public function checkguess($guess)
    {
        if($guess > $this->randomNumber){
             echo "To high";

        }elseif($guess < $this->randomNumber){
             echo "To low";
        }else{
             echo "Correct";
             exit;
        }
    }
}
?>