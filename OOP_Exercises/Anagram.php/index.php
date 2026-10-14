<?php 
class WordManager
{
    private $words=[];
     public function __construct($file)
     {
            $json=file_get_contents("words.json");
            $this->words=json_decode($json,true);
     }

     public function getRandomWord()
     {
            $index=rand(0,count($this->words)-1);
            return $this->words[$index];
     }

     public function shuffleWord($word)
     {
            return str_shuffle($word);
     }
     public function displayshuffledWord($shuffledWord)
     {
            echo "Shuffled Word: ".$shuffledWord."\n";
     }
}

class Player
{
    private $score;

    public function __construct()
    {
        $this->score=0;
    }

    public function getscore()
    {
        return $this->score;
    }
    public function incrementscore()
    {
        return $this->score++;
    }
}

class AnagramGame
{
        private $wordmanager;
        private $player;

        public function __construct()
        {
            $this->wordmanager= new WordManager("words.json");
            $this->player= new Player();
            $totalRounds=5;
        }

        public function startGame()
        {
            echo "      Anagram Game    \n";

            for($i = 1; $i <= 5; $i++)
                {
                    $word=$this->wordmanager->getRandomWord();
                    $shuffledWord=$this->wordmanager->shuffleWord($word);
                    $this->wordmanager->displayshuffledWord($shuffledWord);
                    $answer=readline("Your Answer: ");
                    if(strtolower($answer)===strtolower($word))
                    {
                        echo "Correct! \n";
                        $this->player->incrementscore();
                    }
                    else
                    {
                        echo "Incorrect! The correct word was: ".$word."\n";
                    }
                }
            echo "Game Over! Your final score is: ".$this->player->getscore()."\n";
        }
}

$game=new AnagramGame();
$game->startGame();