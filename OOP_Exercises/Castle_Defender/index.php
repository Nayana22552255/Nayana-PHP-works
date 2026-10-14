<?php

// clas Castle
class Castle
{
    private $name;
    private $health;

    public function __construct($name, $health)
    {
        $this->name = $name;
        $this->health = $health;
    }

    public function getHealth()
    {
        return $this->health;
    }

    public function getName()
    {
        return $this->name;
    }

    public function takeDamage($damage)
    {
        $this->health -= $damage;
        if ($this->health < 0) {
            $this->health = 0;
        }
    }

    public function isdestroyed()
    {
        return $this->health <= 0;
    }
}


// class Enemy
class Enemy
{
    private $name;
    private $damage;

    public function __construct($name, $damage)
    {
        $this->name = $name;
        $this->damage = $damage;
    }

    public function getEName()
    {
        return $this->name;
    
    }
    public function getDamage()
    {
        return $this->damage;
    }
}


//clas QuestionManager
class QuestionManager
{
        private $questions=[];
        
        public function __construct($file)
        {
            if(!file_exists($file))
                {
                    throw new Exception("File not found: $file");
                }

            $jsonData = file_get_contents($file);
            $this->questions = json_decode($jsonData, true);       //convert json into php array

            if(!is_array($this->questions))
                {
                    throw new Exception("Invalid JSON format in file: $file");
                }

            if(count($this->questions) == 0)
                {
                    throw new Exception("Not enough questions in file: $file");
                }
        }

        public function getRandomQuestion()
        {
            $index=rand(0, count($this->questions)-1);
            return $this->questions[$index];
        }
}

//class Game

class Game
{
    private $castle;
    private $enemies;
    private $questionManager;
    private $score;

    public function __construct()
    {
        $this->castle= new Castle("Kings castle", 100);
        $this->enemies = [
            new Enemy("Goblin", 10),
            new Enemy("Orc", 20),
            new Enemy("Dragon", 30)
        ];
        $this->questionManager = new QuestionManager("questions.json");
        $this->score = 0;
    }

    public function start()
    {
            echo "\n";
            echo "==============================\n";
            echo "========Castele Defender========\n";
            echo "==============================\n";

            echo "\n";
            echo "🏰 Castle \n";
            echo $this->castle->getName() . "\n";

            echo "❤️ Health \n";
            echo $this->castle->getHealth() . "\n";

            echo "🏆 Score \n";
            echo $this->score . "\n";
            echo "\n";


            foreach($this->enemies as $enemy)
                {
                    if($this->castle->isdestroyed())
                        {
                            break;
                        }


                        $this->fightEnemy($enemy);

                }
                $this->displayresult();
    }

    private function fightEnemy($enemy)
    {
        echo "\n";
        echo "------------------------------\n";

        echo "👾 Enemy \n";
        echo $enemy->getEName()."\n";

        echo "💥 Damage \n";
        echo $enemy->getDamage()."\n";

        echo "❤️ Castle Health \n";
        echo $this->castle->getHealth()."\n";

        echo "------------------------------\n";


        while(!$this->castle->isdestroyed())
            {
                $question = $this->questionManager->getRandomQuestion();
                echo "\n";

                echo "Answer the question to defeat the enemy \n";
                echo $question['question'] . "\n";

                $answer = readline("Your answer: ");
                $answer=strtolower(trim($answer));

                $correctAnswer=strtolower(trim($question['answer']));           //get the correct answer

                if($answer === $correctAnswer)                                  //check for answer
                    {
                        echo "Correct answer\n";
                        echo $enemy->getEName() . " has been defeated!\n";
                        $this->score++;
                        break;
                    }
                    else
                        {
                            echo "\n";
                            echo "Wrong answer \n";
                            $this->castle->takeDamage($enemy->getDamage());

                            echo "💥".$enemy->getEName()." attacks the castle!\n";

                            echo "❤️ Castle Health: ".$this->castle->getHealth()."\n";


                            if($this->castle->isdestroyed())
                                {
                                    echo "\n";
                                    echo "🏰 The castle has been destroyed! Game Over.\n";
                                    break;
                                }

                                echo "\n";
                                echo "Try again \n";
                        }
               
            }
    }

    private function displayresult()
    {
        echo "\n";
        echo "==============================\n";
        echo "========Game Result========\n";
        echo "==============================\n";


        if($this->castle->isdestroyed())
            {
                echo "🏆 YOU WIN \n";
                echo "All members have been defeated\n";

            }
            else
                {
                    echo " Game Over\n";
                }


            echo "\n";
            echo " Final Health:". $this->castle->gethealth()."\n";
            echo "Final Score: ".$this->score. "\n";
            echo "\n";

    }

}

$game=new Game();
$game->start();