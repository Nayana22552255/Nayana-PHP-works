
<?php

class SOSGame
{
    private array $board;
    private int $size = 3;
    private array $scores;
    private int $currentPlayer = 1;

    //  Initialize the game
    public function __construct()
    {
        $this->board = array_fill(
            0,
            $this->size,
            array_fill(0, $this->size, " ")
        );

        $this->scores = [
            1 => 0,
            2 => 0
        ];
    }

    //  Display the board
    public function displayBoard(): void
    {
        echo "\n    1   2   3\n";

        for ($row = 0; $row < $this->size; $row++) {
            echo "  +---+---+---+\n";
            echo ($row + 1) . " |";

            for ($col = 0; $col < $this->size; $col++) {
                echo " " . $this->board[$row][$col] . " |";
            }

            echo "\n";
        }

        echo "  +---+---+---+\n";
        echo "Player 1 Score: " . $this->scores[1] . "\n";
        echo "Player 2 Score: " . $this->scores[2] . "\n";
    }

    //  Count all SOS sequences on the board
    private function countSOS(): int
    {
        $count = 0;

        // Right, down, diagonal down-right, diagonal down-left
        $directions = [
            [0, 1],
            [1, 0],
            [1, 1],
            [1, -1]
        ];

        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {

                foreach ($directions as [$dr, $dc]) {

                    $endRow = $row + 2 * $dr;
                    $endCol = $col + 2 * $dc;

                    // Check whether the three cells are inside the board
                    if (
                        $endRow < 0 || $endRow >= $this->size ||
                        $endCol < 0 || $endCol >= $this->size
                    ) {
                        continue;
                    }

                    $first = $this->board[$row][$col];
                    $second = $this->board[$row + $dr][$col + $dc];
                    $third = $this->board[$endRow][$endCol];

                    if (
                        $first === "S" &&
                        $second === "O" &&
                        $third === "S"
                    ) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    //  Play one move
    private function makeMove(
        int $row,
        int $col,
        string $letter
    ): bool {

        // Check board position
        if (
            $row < 0 || $row >= $this->size ||
            $col < 0 || $col >= $this->size
        ) {
            echo "Invalid position! Choose a number from 1 to 3.\n";
            return false;
        }

        // Check whether the cell is occupied
        if ($this->board[$row][$col] !== " ") {
            echo "That position is already occupied!\n";
            return false;
        }

        // Count sequences before placing the letter
        $before = $this->countSOS();

        // Place S or O
        $this->board[$row][$col] = $letter;

        // Count sequences after placing the letter
        $after = $this->countSOS();

        // Award points for newly created SOS sequences
        $points = $after - $before;

        if ($points > 0) {
            $this->scores[$this->currentPlayer] += $points;

            echo "SOS formed! Player "
                . $this->currentPlayer
                . " earns " . $points . " point(s)!\n";
        }

        return true;
    }

    // : Check whether the board is full
    private function isBoardFull(): bool
    {
        for ($row = 0; $row < $this->size; $row++) {
            for ($col = 0; $col < $this->size; $col++) {
                if ($this->board[$row][$col] === " ") {
                    return false;
                }
            }
        }

        return true;
    }

    //  Display the final result
    private function showWinner(): void
    {
        echo "\n========== GAME OVER ==========\n";
        echo "Player 1 Score: " . $this->scores[1] . "\n";
        echo "Player 2 Score: " . $this->scores[2] . "\n";

        if ($this->scores[1] > $this->scores[2]) {
            echo "Player 1 wins!\n";
        } elseif ($this->scores[2] > $this->scores[1]) {
            echo "Player 2 wins!\n";
        } else {
            echo "The game is a draw!\n";
        }
    }

    //  Start and control the game
    public function start(): void
    {
        echo "===== SOS GAME =====\n";
        echo "Enter row and column (1 to 3).\n";
        echo "Choose S or O for each move.\n";

        while (!$this->isBoardFull()) {

            $this->displayBoard();

            echo "\nPlayer " . $this->currentPlayer . "'s turn\n";

            echo "Enter row: ";
            $rowInput = trim(fgets(STDIN));

            echo "Enter column: ";
            $colInput = trim(fgets(STDIN));

            echo "Enter letter (S/O): ";
            $letter = strtoupper(trim(fgets(STDIN)));

            // Validate input before using it
            if (
                !ctype_digit($rowInput) ||
                !ctype_digit($colInput) ||
                !in_array($letter, ["S", "O"], true)
            ) {
                echo "Invalid input! Try again.\n";
                continue;
            }

            $row = (int)$rowInput - 1;
            $col = (int)$colInput - 1;

            if ($this->makeMove($row, $col, $letter)) {

                // Switch players after a valid move
                $this->currentPlayer =
                    ($this->currentPlayer === 1) ? 2 : 1;
            }
        }

        $this->displayBoard();
        $this->showWinner();
    }
}

//Create an object and start the game
$game = new SOSGame();
$game->start();

?>
