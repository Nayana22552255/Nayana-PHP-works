
<?php

class WordSearch
{
    private int $size = 10;

    private array $grid = [];

    private array $words = ['CAT', 'DOG', 'PHP', 'JAVA'];

    private array $foundWords = [];

    // Eight possible directions
    private array $directions = [
        [0, 1],    // Right
        [0, -1],   // Left
        [1, 0],    // Down
        [-1, 0],   // Up
        [1, 1],    // Diagonal down-right
        [1, -1],   // Diagonal down-left
        [-1, 1],   // Diagonal up-right
        [-1, -1]   // Diagonal up-left
    ];

    public function __construct()
    {
        echo "Welcome to Word Search Puzzle!\n";

        // Create an empty grid
        for ($r = 0; $r < $this->size; $r++) {
            $this->grid[$r] = array_fill(0, $this->size, '');
        }

        // Place words randomly
        $this->placeWords();

        // Fill the remaining empty cells
        $this->fillEmptyCells();
    }

    // Place each word randomly in the grid
    private function placeWords(): void
    {
        foreach ($this->words as $word) {

            $placed = false;

            // Try different random positions
            for ($attempt = 0; $attempt < 1000; $attempt++) {

                $startRow = random_int(0, $this->size - 1);
                $startCol = random_int(0, $this->size - 1);

                $directions = $this->directions;
                shuffle($directions);

                foreach ($directions as $direction) {

                    $rowStep = $direction[0];
                    $colStep = $direction[1];

                    $endRow = $startRow
                        + (strlen($word) - 1) * $rowStep;

                    $endCol = $startCol
                        + (strlen($word) - 1) * $colStep;

                    // Check whether the word fits in the grid
                    if (
                        $endRow < 0 || $endRow >= $this->size ||
                        $endCol < 0 || $endCol >= $this->size
                    ) {
                        continue;
                    }

                    $canPlace = true;

                    // Check for conflicting letters
                    for ($i = 0; $i < strlen($word); $i++) {

                        $r = $startRow + ($i * $rowStep);
                        $c = $startCol + ($i * $colStep);

                        $existing = $this->grid[$r][$c];

                        if (
                            $existing !== '' &&
                            $existing !== $word[$i]
                        ) {
                            $canPlace = false;
                            break;
                        }
                    }

                    // Place the word if there is no conflict
                    if ($canPlace) {

                        for ($i = 0; $i < strlen($word); $i++) {

                            $r = $startRow + ($i * $rowStep);
                            $c = $startCol + ($i * $colStep);

                            $this->grid[$r][$c] = $word[$i];
                        }

                        $placed = true;
                        break;
                    }
                }

                if ($placed) {
                    break;
                }
            }

            if (!$placed) {
                throw new RuntimeException(
                    "Could not place the word: $word"
                );
            }
        }
    }

    // Fill unused cells with random letters
    private function fillEmptyCells(): void
    {
        for ($r = 0; $r < $this->size; $r++) {
            for ($c = 0; $c < $this->size; $c++) {

                if ($this->grid[$r][$c] === '') {
                    $this->grid[$r][$c] =
                        chr(random_int(65, 90));
                }
            }
        }
    }

    // Display the grid with row and column numbers
    public function displayGrid(): void
    {
        echo "\nWord Search Grid:\n\n";

        echo "     ";

        for ($c = 1; $c <= $this->size; $c++) {
            echo str_pad((string)$c, 3, ' ', STR_PAD_LEFT);
        }

        echo "\n";

        foreach ($this->grid as $r => $row) {

            echo str_pad((string)($r + 1), 3, ' ', STR_PAD_LEFT);
            echo " | ";

            foreach ($row as $letter) {
                echo $letter . "  ";
            }

            echo "\n";
        }
    }

    // Display the target words
    public function displayWords(): void
    {
        echo "\nWords to Find:\n";

        foreach ($this->words as $word) {

            if (in_array($word, $this->foundWords)) {
                echo "$word - Found\n";
            } else {
                echo "$word\n";
            }
        }
    }

    // Check the selected starting and ending positions
    public function checkPosition(
        int $startRow,
        int $startCol,
        int $endRow,
        int $endCol
    ): void {

        // Validate positions
        if (
            $startRow < 1 || $startRow > $this->size ||
            $endRow < 1 || $endRow > $this->size ||
            $startCol < 1 || $startCol > $this->size ||
            $endCol < 1 || $endCol > $this->size
        ) {
            echo "Invalid position! Use numbers from 1 to 10.\n";
            return;
        }

        // Convert to zero-based array indexes
        $startRow--;
        $startCol--;
        $endRow--;
        $endCol--;

        $rowDiff = $endRow - $startRow;
        $colDiff = $endCol - $startCol;

        // Only horizontal, vertical and diagonal lines are allowed
        if (
            ($rowDiff === 0 && $colDiff === 0) ||
            ($rowDiff !== 0 && $colDiff !== 0 &&
             abs($rowDiff) !== abs($colDiff))
        ) {
            echo "Invalid direction! Choose a straight line.\n";
            return;
        }

        $rowStep = $rowDiff <=> 0;
        $colStep = $colDiff <=> 0;

        $word = '';
        $row = $startRow;
        $col = $startCol;

        // Collect letters between the selected positions
        while (true) {

            $word .= $this->grid[$row][$col];

            if ($row === $endRow && $col === $endCol) {
                break;
            }

            $row += $rowStep;
            $col += $colStep;
        }

        echo "\nLetters found: $word\n";

        // Check the target word list
        if (!in_array($word, $this->words)) {
            echo "Incorrect! This is not a target word.\n";
            return;
        }

        // Prevent duplicate answers
        if (in_array($word, $this->foundWords)) {
            echo "You have already found $word!\n";
            return;
        }

        $this->foundWords[] = $word;

        echo "Correct! You found $word.\n";
    }

    // Run the game
    public function play(): void
    {
        while (count($this->foundWords) < count($this->words)) {

            $this->displayGrid();
            $this->displayWords();

            echo "\nEnter starting row (0 to exit): ";
            $input = trim(fgets(STDIN));

            if ($input === '0') {
                echo "Game ended.\n";
                return;
            }

            if ($input === '' || !ctype_digit($input)) {
                echo "Enter a valid number.\n";
                continue;
            }

            $startRow = (int)$input;

            echo "Enter starting column: ";
            $startCol = trim(fgets(STDIN));

            echo "Enter ending row: ";
            $endRow = trim(fgets(STDIN));

            echo "Enter ending column: ";
            $endCol = trim(fgets(STDIN));

            if (
                !ctype_digit($startCol) ||
                !ctype_digit($endRow) ||
                !ctype_digit($endCol)
            ) {
                echo "Enter valid numbers.\n";
                continue;
            }

            $this->checkPosition(
                $startRow,
                (int)$startCol,
                (int)$endRow,
                (int)$endCol
            );
        }

        echo "\nCongratulations! You found all the words!\n";
    }
}

// Create an object and start the game
$game = new WordSearch();
$game->play();

?>
