<?php
class Mastermind
{
    private $secretcode;
    private $colours;
    private $maxattempts;

    public function __construct()
    {
        $this->colours= ['R','G','B','Y','O','P'];
        $this->maxattempts=10;

        $this->generateSecretcode();
    }


    public function generateSecretcode()
    {
        $this->secretcode= [];

        for($i=0; $i<4; $i++)
            {
                $randomindex=array_rand($this->colours);

                $this->secretcode[]=$this->colours[$randomindex];
            }
    }

    public function displayRules()
    {
        echo "====================================\n";
        echo "       MASTERMIND GAME\n";
        echo "====================================\n";
        echo "Guess the secret 4-color code.\n";
        echo "Available colors: R G B Y O P\n";
        echo "R = Red, G = Green, B = Blue\n";
        echo "Y = Yellow, O = Orange, P = Purple\n";
        echo "Enter four letters without spaces.\n";
        echo "Example: RGBY\n";
        echo "You have {$this->maxattempts} attempts.\n";
        echo "====================================\n";
    }

    public function getfeedback($guess)
    {
            $exact=0;
            $secrectremaining=[];
            $guessremaining=[];
            
            for($i=0; $i<4; $i++)
                {
                    if($guess[$i]=== $this->secretcode[$i])
                        {
                            $exact++;
                        }
                        else{
                            $secrectremaining[]= $this->secretcode[$i];
                            $guessremaining[]=$guess[$i];
                        }
                }

                $wrongposition =0;

                foreach ($guessremaining as $color) {
                    $index =array_search($color, $secrectremaining, true);

                    if($index != false)
                        {
                            $wrongposition++;

                            unset($secrectremaining[$index]);
                        }

                }
                return [$exact, $wrongposition];
    }

    public function play()
    {
        $this->displayRules();

        for($attempts =1; $attempts <= $this->maxattempts; $attempts++)
            {
                echo "\nAttempts $attempts of {$this->maxattempts}\n";

                $guess=strtoupper(trim(readline("Enter your guess: ")));


                if(
                    strlen($guess)!== 4 ||
                    strspn($guess, implode('', $this->colours)) !==4
                )
                {
                    echo "Invalid input! Enter Exactly 4 valid colour letters. \n";
                    $attempts--;
                    continue;
                }

                $guessArray=str_split($guess);

                [$exact,$wrongposition] = $this->getfeedback($guessArray);

                echo "exact matches : $exact \n";
                echo "Wrong-position matches: $wrongposition\n";


                if($exact == 4)
                    {
                        echo "\nCongratulations! You cracked the code!\n";
                        echo "You won in $attempt attempt(s).\n";
                        return;
                    }
            }
                echo "\nGame over! You used all your attempts.\n";
                echo "The secret code was: ";
                echo implode('', $this->secretcode) . "\n";


    }
}

$game= new Mastermind();
$game->play();