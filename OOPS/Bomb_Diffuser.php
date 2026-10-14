<?php

class BombDiffuser
{

        private $wires;
        private $correctWire;
        private $attempts;
        private $gameover;

        public function __construct()
        {
            $this->wires = ["Red","Blue","Green","Yellow"];
            $this->correctWire = rand(1,4);
            $this->attempts=3;
            $this->gameover=false;
            
        }
        public function displayWires()
        {
            echo "===============================\n";
            echo "        Bomb Diffuser          \n";
            echo "===============================\n";

            echo "Attempts Left: ". $this->attempts. "\n";

            foreach($this->wires as $key => $wire)
                {
                    echo ($key + 1). " .".$wire."\n";       //key used to display the index start from 1
                }

        }

        public function diffuse($choice)
        {
            if($choice == $this->correctWire)
                {
                    echo "Device successfully diffused!\n";
                    $this->gameover= true;
                }
                else
                    {
                        $this->attempts= $this->attempts-1;
                        echo "Wrong wire! Attempts left: ". $this->attempts. "\n";

                    }
                if($this->attempts <= 0)
                    {
                        echo "Game Over! You have run out of attempts.\n";
                    }
        }

        public function startgame()
        {
            while(!$this->gameover)             //continue palying while the gameis not over
            {
                $this->displayWires();
                echo "choose a wire (1,4):";
                $choice = trim(fgets(STDIN));
                
                if($choice <1 || $choice >4)
                    {
                        echo "invalid choice! please enter a correct one.\n";
                        continue;
                    }
                $this->diffuse($choice);
          }
        }

}

$game= new BombDiffuser();
$game->startgame();