<?php 
class Game2048
{
    private $board;
    private $score;

    public function __construct()
    {
        $this->board = [
            [0, 0, 0, 0],
            [0, 0, 0, 0],
            [0, 0, 0, 0],
            [0, 0, 0, 0]
        ];

        $this->score = 0;
    }
    public function displayBoard()
    {
        echo "\n";

        for ($i = 0; $i < 4; $i++)
            {
                for ($j = 0; $j < 4; $j++){
                    echo str_pad($this->board[$i][$j], 5, " ", STR_PAD_LEFT);
                }
                echo "\n";
            }
        echo "Score: " . $this->score . "\n";
    }

    public function addRandomTile()
    {
        $emptyCells = [];
        for ($i = 0;$i < 4; $i++){
            for($j = 0;$j < 4; $j++){
                if ($this->board[$i][$j] == 0){
                    $emptyCells[] = [$i, $j];
                }
            }
        }
        if (count($emptyCells) == 0){
            return;
        }
        $randomIndex = array_rand($emptyCells);

        $row = $emptyCells[$randomIndex][0];
        $column = $emptyCells[$randomIndex][1];

        $value = (rand(1, 10) <= 9) ? 2 : 4;

        $this->board[$row][$column] = $value;
    }
    public function move($direction)
{
    $oldBoard = $this->board;

    if ($direction == "a") {
        $this->moveLeft();
    }
    elseif ($direction == "d") {
        $this->moveRight();
    }
    elseif ($direction == "w") {
        $this->moveUp();
    }
    elseif ($direction == "s") {
        $this->moveDown();
    }
    else {
        echo "Invalid move!\n";
        return false;
    }

    if ($oldBoard != $this->board) {
        return true;
    }

    return false;
}

   private function moveLeft()
{
    for ($i = 0; $i < 4; $i++) {

        $row = $this->board[$i];

        $newRow = [];

        foreach ($row as $value) {
            if ($value != 0) {
                $newRow[] = $value;
            }
        }

        $newRow = $this->mergeRow($newRow);

        $this->board[$i] = $newRow;
    }
}
    private function moveRight()
    {
        for($i = 0;$i < 4; $i++){
            $row = $this->board[$i];
            $newRow = [];

            foreach($row as $value){
                if ($value != 0){
                    $newRow[] = $value;
                }
            }
            $newRow = array_reverse($newRow);
            $newRow = $this->mergeRow($newRow);
            $newRow = array_reverse($newRow);
            $this->board[$i] = $newRow;
        }
    }
    private function moveUp()
{
    for ($j = 0; $j < 4; $j++) {

        $column = [];

        for ($i = 0; $i < 4; $i++) {
            if ($this->board[$i][$j] != 0) {
                $column[] = $this->board[$i][$j];
            }
        }

        $column = $this->mergeRow($column);

        for ($i = 0; $i < 4; $i++) {
            $this->board[$i][$j] = $column[$i];
        }
    }
}
private function moveDown()
    {
    for ($j = 0; $j < 4; $j++) {

        $column = [];

        for ($i = 0; $i < 4; $i++) {
            if ($this->board[$i][$j] != 0) {
                $column[] = $this->board[$i][$j];
            }
        }

        $column = array_reverse($column);

        $column = $this->mergeRow($column);

        $column = array_reverse($column);

        for ($i = 0; $i < 4; $i++) {
            $this->board[$i][$j] = $column[$i];
        }
    }
}
    private function mergeRow($row)
    {
        $newRow = [];
        $i = 0;
        while ($i < count($row)) {
            if ($i + 1 < count($row) && $row[$i] == $row[$i + 1]) {
                $newValue = $row[$i] * 2;
                $newRow[] = $newValue;
                $this->score += $newValue;
                $i += 2;
            }
            else{
                $newRow[] = $row[$i];
                $i++;
            }
        }
        while (count($newRow) < 4){
            $newRow[] = 0;
        }
        return $newRow;
    }
    public function hasWon()
{
    for ($i = 0; $i < 4; $i++) {

        for ($j = 0; $j < 4; $j++) {

            if ($this->board[$i][$j] == 2048) {
                return true;
            }
        }
    }

    return false;
}
public function isGameOver()
{
    // Check if there is any empty cell
    for ($i = 0; $i < 4; $i++) {

        for ($j = 0; $j < 4; $j++) {

            if ($this->board[$i][$j] == 0) {
                return false;
            }
        }
    }

    // Check horizontal neighboring cells
    for ($i = 0; $i < 4; $i++) {

        for ($j = 0; $j < 3; $j++) {

            if ($this->board[$i][$j] == $this->board[$i][$j + 1]) {
                return false;
            }
        }
    }

    // Check vertical neighboring cells
    for ($i = 0; $i < 3; $i++) {

        for ($j = 0; $j < 4; $j++) {

            if ($this->board[$i][$j] == $this->board[$i + 1][$j]) {
                return false;
            }
        }
    }

    return true;
}

}


$game = new Game2048();

$game->addRandomTile();
$game->addRandomTile();
do {

    $game->displayBoard();

    $direction = strtolower(
        readline("Move (w/a/s/d) or q to quit: ")
    );

    if ($direction == "q") {
        echo "Game ended!\n";
        break;
    }

    $moved = $game->move($direction);

    if ($moved) {

        if ($game->hasWon()) {
            $game->displayBoard();

            echo "\n====================\n";
            echo "      YOU WIN!\n";
            echo "====================\n";
            echo "You created the 2048 tile!\n";

            break;
        }

        $game->addRandomTile();
        if ($game->isGameOver()) {

    $game->displayBoard();

    echo "\n====================\n";
    echo "     GAME OVER!\n";
    echo "====================\n";

    break;
}
    }
    else {
        echo "Invalid move! Try another direction.\n";
    }

} while (true);