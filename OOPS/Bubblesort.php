<?php

class BubbleSortGame
{
    private $numbers;
    private $score;
    private $moves;

    public function __construct()
    {
        $this->numbers = [];
        $this->score = 0;
        $this->moves = 0;
    }

    // Generate random numbers
    public function generateNumbers()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->numbers[] = rand(1, 20);
        }
    }

    // Display numbers
    public function displayNumbers()
    {
        echo "\nNumbers: ";

        foreach ($this->numbers as $number) {
            echo $number . " ";
        }

        echo "\n";
    }

    // Compare and swap
    public function compareAndSwap($position)
    {
        $this->moves++;

        if ($this->numbers[$position] >
            $this->numbers[$position + 1]) {

            $temp = $this->numbers[$position];

            $this->numbers[$position] =
                $this->numbers[$position + 1];

            $this->numbers[$position + 1] =
                $temp;

            $this->score += 10;

            echo "Correct! Numbers swapped.\n";
        }
        else {

            echo "No swap needed. They are already in order.\n";
        }
    }

    // Check whether sorted
    public function isSorted()
    {
        for ($i = 0; $i < count($this->numbers) - 1; $i++) {

            if ($this->numbers[$i] >
                $this->numbers[$i + 1]) {

                return false;
            }
        }

        return true;
    }

    // Display score
    public function displayScore()
    {
        echo "Score: " . $this->score . "\n";
        echo "Moves: " . $this->moves . "\n";
    }
}


// Create game
$game = new BubbleSortGame();


// Generate numbers
$game->generateNumbers();

echo "============================\n";
echo "      BUBBLE SORT GAME\n";
echo "============================\n";


// Game loop
while (!$game->isSorted()) {

    $game->displayNumbers();

    echo "\nChoose adjacent position to compare:\n";

    echo "1 = Position 1 and 2\n";
    echo "2 = Position 2 and 3\n";
    echo "3 = Position 3 and 4\n";
    echo "4 = Position 4 and 5\n";

    echo "\nEnter position: ";

    $position = intval(trim(fgets(STDIN)));

    // Convert user position to array index
    $position = $position - 1;

    // Validate input
    if ($position < 0 || $position > 3) {

        echo "Invalid position!\n";
        continue;
    }

    // Compare and swap
    $game->compareAndSwap($position);

    echo "\n";
}


// Player wins

echo "\n============================\n";
echo "          YOU WIN!\n";
echo "============================\n";

$game->displayNumbers();

$game->displayScore();

?>