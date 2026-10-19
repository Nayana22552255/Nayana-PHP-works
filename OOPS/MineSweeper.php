
<?php

class Minesweeper
{
        private  $size = 5;
        private  $mineCount = 5;

        private  $board = [];
        private  $revealed = [];

        private  $gameOver = false;

    public function __construct()
        {
               $this->createBoard();
               $this->placeMines();
               $this->calculateNumbers();
        }

   
    private function createBoard()                   // Create an empty board and a hidden-cell grid
    {
        for ($row = 0; $row < $this->size; $row++) 
        {
            for ($col = 0; $col < $this->size; $col++)
                 {
                    $this->board[$row][$col] = 0;
                    $this->revealed[$row][$col] = false;
                 }
        }
    }

    private function placeMines()                       // Place mines randomly

    {
        $placed = 0;

        while ($placed < $this->mineCount) 
        {
            $row = random_int(0, $this->size - 1);
            $col = random_int(0, $this->size - 1);

            if ($this->board[$row][$col] !== '*') {
                $this->board[$row][$col] = '*';
                $placed++;
            }
        }
    }

    private function calculateNumbers()                                         // Count the mines surrounding each safe cell

    {
        for ($row = 0; $row < $this->size; $row++) 
            {
            for ($col = 0; $col < $this->size; $col++) 
                {

                if ($this->board[$row][$col] === '*') 
                    {
                        continue;
                }   

                $count = 0;

                for ($rows = $row - 1; $rows <= $row + 1; $rows++) 
                    {
                    for ($column = $col - 1; $column <= $col + 1; $column++) 
                        {

                        if (
                            $rows >= 0 && $rows < $this->size &&
                            $column >= 0 && $column < $this->size &&
                            $this->board[$rows][$column] === '*'
                        ) {
                            $count++;
                        }
                    }
                }

                $this->board[$row][$col] = $count;
            }
        }
    }

    public function displayBoard()                              // Display the board

    {
        echo "\n    ";

        for ($col = 1; $col <= $this->size; $col++)
             {
            echo $col . " ";
            }

        echo "\n";

        for ($row = 0; $row < $this->size; $row++)
         {
            echo ($row + 1) . " | ";

            for ($col = 0; $col < $this->size; $col++)
             {

                if ($this->revealed[$row][$col]) 
                    {
                    echo $this->board[$row][$col] . " ";
                    } 
                    else 
                    {
                    echo "? ";
                    }
            }

            echo "\n";
        }
    }

    private function revealCell( $row,  $col)                       // Reveal a cell using recursion

    {
        if (
            $row < 0 || $row >= $this->size ||                              // Stop if outside the board
            $col < 0 || $col >= $this->size) 
            {
            return;
            }

        if (
            $this->revealed[$row][$col] ||                                      // Stop if already revealed or contains a mine
             $this->board[$row][$col] === '*') 
            {
            return;
            }

        $this->revealed[$row][$col] = true;                                     // Reveal this safe cell


        if ($this->board[$row][$col] !== 0)                                             // If it has neighboring mines, stop here

             {
            return;
            }

        for ($rows = $row - 1; $rows <= $row + 1; $rows++)                                  // Reveal all eight neighboring cells recursively

            {
            for ($column = $col - 1; $column <= $col + 1; $column++) 
                {

                if ($rows === $row && $column === $col) 
                    {
                    continue;
                    }

                $this->revealCell($rows, $column);
            }
        }
    }

    private function hasWon()                                         // Check whether the player has won

    {
        $revealedCount = 0;
        $safeCellCount = ($this->size * $this->size) - $this->mineCount;

        for ($row = 0; $row < $this->size; $row++) 
            {
            for ($col = 0; $col < $this->size; $col++) 
                {
                if (
                    $this->revealed[$row][$col] &&
                    $this->board[$row][$col] !== '*')
                     {
                        $revealedCount++;
                }
            }
        }

        return $revealedCount === $safeCellCount;
    }

    private function revealAll()                                        // Reveal the entire board when the game ends

    {
        for ($row = 0; $row < $this->size; $row++)
         {
            for ($col = 0; $col < $this->size; $col++) 
            {
                $this->revealed[$row][$col] = true;
            }
        }
    }

    public function play()
    {
        echo "===== MINESWEEPER GAME =====\n";
        echo "Board size: {$this->size} x {$this->size}\n";
        echo "Number of mines: {$this->mineCount}\n";
        echo "Rules for MineSwapper\n";
        echo "0 — A safe cell with no adjacent mines\n";
        echo "1, 2, 3 — A safe cell showing the number of adjacent mines.\n";
        echo "* — A mine, shown when the game ends.\n";
        echo "Enter row and column between 1 and {$this->size}.\n";

        while (!$this->gameOver) 
        {
            $this->displayBoard();

            $input = readline("\nEnter row and column (e.g. 2 3): ");

            $parts = preg_split('/\s+/', trim($input));

            if (
                count($parts) !== 2 ||
                !ctype_digit($parts[0]) ||
                !ctype_digit($parts[1]))
             {
                echo "Invalid input! Enter two numbers, such as 2 3.\n";
                continue;
             }

            $row =  $parts[0] - 1;
            $col =  $parts[1] - 1;

            if (
                $row < 0 || $row >= $this->size ||
                $col < 0 || $col >= $this->size)
           {
                echo "Invalid position! Choose values from 1 to {$this->size}.\n";
                continue;
            }

            if ($this->revealed[$row][$col]) 
            {
                echo "That cell is already revealed. Try another cell.\n";
                continue;
            }

            // Check whether the selected cell is a mine
            if ($this->board[$row][$col] === '*') {
                $this->revealAll();
                $this->displayBoard();

                echo "\nBOOM! You hit a mine. Game over!\n";
                $this->gameOver = true;
                break;
            }

            // Reveal the selected safe cell
            $this->revealCell($row, $col);

            if ($this->hasWon())                                        // Check whether every safe cell has been revealed
            {
                $this->revealAll();
                $this->displayBoard();

                echo "\nCongratulations! You won the game!\n";
                $this->gameOver = true;
            }
        }
    }
}

$game = new Minesweeper();
$game->play();

?>
