<?php
class Wordguessing{
    private $words=["kerala","mango","bubbletea","watermelon","teacher","staff","buttler"];
    private $word;
    private $guessedword;
    private $attempts=6;

    public function  selectedword()           //select a random word
    {
        $index=rand(0,count($this->words)-1);
        $this->word =$this->words[$index];
        echo "word selected\n";
    }

    public function hiddenword()        //hide the word by using the underscore
    {
        $this->guessedword=str_repeat("_",strlen($this->word));
    }

    public function displayword()               //for displaying the guessed word
    {
        echo "\nWord  ";
        for($i = 0; $i < strlen( $this->guessedword ); $i++)
        {
            echo $this->guessedword[$i]. " ";
        }
            echo "\n";
    
    }
    public function getplayerguess()        //get letters from player
    {
        $letter=readline("enter a letter : ");
        return strtolower($letter);
    }

    public function checkletter($letter)        //checking the letters
    {
            $found=false;
            for ($i =0; $i < strlen($this->word); $i++)
                {
                    if($this->word[$i] == $letter)
                        {
                            $this->guessedword[$i]= $letter;
                            $found =true;
                        }
                }
                if($found)
                    {
                        echo "correct guess\n";
                    
                    }else{
                        echo "wrong guess\n";
                        $this->attempts--;              //reducing the number of attempts
                    }
    }
    public function iswon()                                  //check for if the player won or not
    {
        return $this->guessedword == $this->word;
    }

    public function isgameover()                                   //check for if the game os over
    {
        return $this->attempts <= 0;
    }
    public function displayattempts()                               //display the number of attempts
    {
        echo "attempts left are:".$this->attempts. "\n";

    }
    public function displayallword()
    {
        echo "The word is  ". $this->word."\n";
    }
        
}
?>
