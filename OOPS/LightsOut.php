
<?php

class LightsOutGame
{
    private array $grid = [];
    private int $size = 3;
    private int $moves = 0;

    // Constructor
    public function __construct()
    {
        // Create a grid with all lights OFF
        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                $this->grid[$row][$col] = false;
            }
        }

        // Randomly toggle lights to create a solvable puzzle
        for ($i = 0; $i < 6; $i++) {
            $row = random_int(0, $this->size - 1);
            $col = random_int(0, $this->size - 1);

            $this->toggleLights($row, $col);
        }
    }

    // Display the grid
    public function displayGrid(): void
    {
        echo "\nLights Out Game\n";
        echo "----------------\n";

        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                echo $this->grid[$row][$col] ? " ON " : " OFF";
            }
            echo "\n";
        }

        echo "Moves: " . $this->moves . "\n";
    }

    // Toggle a light and its neighboring lights
    private function toggleLights(int $row, int $col): void
    {
        // Current cell, up, down, left, right
        $positions = [
            [$row, $col],
            [$row - 1, $col],
            [$row + 1, $col],
            [$row, $col - 1],
            [$row, $col + 1]
        ];

        foreach ($positions as [$row, $col]) {
            if (
                $row >= 0 && $row < $this->size &&
                $col >= 0 && $col < $this->size
            ) {
                $this->grid[$row][$col] = !$this->grid[$row][$col];
            }
        }
    }

    // Check whether all lights are OFF
    public function isWon(): bool
    {
        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                if ($this->grid[$row][$col] === true) {
                    return false;
                }
            }
        }

        return true;
    }

    // Start the game
    public function play(): void
    {
        echo "Welcome to Lights Out!\n";
        echo "Enter row and column numbers from 1 to 3.\n";
        echo "Enter 0 for the row to quit.\n";

        while (!$this->isWon()) {
            $this->displayGrid();

            $rowInput = readline("Enter row (1-3): ");

            if ($rowInput === "0") {
                echo "Game ended. Thanks for playing!\n";
                return;
            }

            $colInput = readline("Enter column (1-3): ");

            if (
                !ctype_digit($rowInput) ||
                !ctype_digit($colInput) ||
                (int)$rowInput < 1 ||
                (int)$rowInput > $this->size ||
                (int)$colInput < 1 ||
                (int)$colInput > $this->size
            ) {
                echo "Invalid input! Enter numbers from 1 to 3.\n";
                continue;
            }

            // Convert to zero-based array indexes
            $row = (int)$rowInput - 1;
            $col = (int)$colInput - 1;

            $this->toggleLights($row, $col);
            $this->moves++;
        }

        $this->displayGrid();

        echo "\nCongratulations! You turned all lights OFF!\n";
        echo "Total moves: " . $this->moves . "\n";
    }
}

// Create the game object
$game = new LightsOutGame();

// Run the game
$game->play();

?>
