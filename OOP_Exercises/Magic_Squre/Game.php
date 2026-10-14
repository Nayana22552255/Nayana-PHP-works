
<?php

require_once __DIR__ . "/Player.php";
require_once __DIR__ . "/Magicsquare.php";
require_once __DIR__ . "/Puzzle.php";
require_once __DIR__ . "/ScoreManager.php";

class Game
{
    private $magicSquare;
    private $player;
    private $puzzle;
    private $scoremanager;

    public function __construct($playername)
    {
        $this->player = new Players($playername);
        $this->magicSquare = new MagicSquare(3);

        $this->scoremanager = new ScoreManger(
            __DIR__ . "/Data.json"
        );

        $this->scoremanager->loadData();
    }

    public function Sartgame()
    {
        $this->magicSquare->generateSquare();

        $square = $this->magicSquare->getSquare();

        $this->puzzle = new Puzzle($square);
        $this->puzzle->Createpuzzle();

        echo "\nWelcome, "
            . $this->player->getPlayerName()
            . "!\n";

        echo "\nComplete the magic square:\n";

        $this->puzzle->Displaypuzzle();

        $this->getplayerinput();

        if ($this->checkanswer()) {
            echo "\nCongratulations! Correct answer.\n";

            $this->calculatescore();
        } else {
            echo "\nSorry, your answers are incorrect.\n";
            echo "Correct magic square:\n";

            $this->magicSquare->DisplaySquare();
        }

        // Display player details
        echo "\nPlayer: "
            . $this->player->getPlayerName();

        echo "\nScore: "
            . $this->player->getPlayerScore();

        echo "\nAttempts: "
            . $this->player->getAttempts() . "\n";

        // Save details whether the answer is correct or wrong
        $this->scoremanager->saveplayer($this->player);
        $this->scoremanager->SaveScore($this->player);
        $this->scoremanager->saveData();


    }

    public function getplayerinput()
    {
        $positions = $this->puzzle->getmissingposition();

        foreach ($positions as $position) {
            $row = $position[0];
            $column = $position[1];

            $number = (int) trim(
                readline(
                    "Enter number for row " .
                    ($row + 1) .
                    ", column " .
                    ($column + 1) . ": "
                )
            );

            // IMPORTANT: Insert the entered number
            // into the correct puzzle position.
            $this->puzzle->fillnumber(
                $row,
                $column,
                $number
            );
        }

        // One attempt means one complete submission.
        $this->player->IncreaseAttempts();
    }

    public function checkanswer()
    {
        $completedSquare = $this->puzzle->getpuzzle();

        return $this->magicSquare->CompleteCheckMagicSquare(
            $completedSquare
        );
    }

    public function calculatescore()
    {
        $attempts = $this->player->getAttempts();

        if ($attempts == 1) {
            $this->player->addScore(10);
        } elseif ($attempts == 2) {
            $this->player->addScore(7);
        } else {
            $this->player->addScore(5);
        }
    }

    public function showscore()
    {
        $this->scoremanager->DisplayScore();
    }
}
?>
