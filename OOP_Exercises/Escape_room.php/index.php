<?php



// 1. PLAYER CLASS


class Player
{
    private $name;
    private $items = [];
    private $score = 0;
    private $attempts = 0;

    public function __construct($name)
    {
        $this->name = $name;
    }

    // Get player name
    public function getName()
    {
        return $this->name;
    }

    // Add an item to inventory
    public function addItem($item)
    {
        $this->items[] = $item;

        echo "\nItem added to inventory: $item\n";
    }

    // Check whether player has an item
    public function hasItem($item)
    {
        return in_array($item, $this->items);
    }

    // Display inventory
    public function showInventory()
    {
        echo "\n========== INVENTORY ==========\n";

        if (empty($this->items)) {
            echo "Inventory is empty.\n";
        } else {

            foreach ($this->items as $item) {
                echo "- $item\n";
            }
        }

        echo "===============================\n";
    }

    // Add score
    public function addScore($score)
    {
        $this->score += $score;
    }

    // Get score
    public function getScore()
    {
        return $this->score;
    }

    // Increase attempts
    public function increaseAttempt()
    {
        $this->attempts++;
    }

    // Get attempts
    public function getAttempts()
    {
        return $this->attempts;
    }
}



// 2. PUZZLE CLASS


class Puzzle
{
    private $name;
    private $question;
    private $answer;
    private $hint;
    private $solved = false;

    public function __construct($name, $question, $answer, $hint)
    {
        $this->name = $name;
        $this->question = $question;
        $this->answer = $answer;
        $this->hint = $hint;
    }

    // Display puzzle
    public function showPuzzle()
    {
        echo "\n====================================\n";
        echo "Puzzle: " . $this->name . "\n";
        echo "====================================\n";
        echo $this->question . "\n";
    }

    // Check answer
    public function checkAnswer($userAnswer)
    {
        if (strtolower(trim($userAnswer)) == strtolower(trim($this->answer))) {

            $this->solved = true;

            return true;
        }

        return false;
    }

    // Display hint
    public function showHint()
    {
        echo "\nHint: " . $this->hint . "\n";
    }

    // Check whether puzzle is solved
    public function isSolved()
    {
        return $this->solved;
    }

    // Get puzzle name
    public function getName()
    {
        return $this->name;
    }
}



// 3. ESCAPE ROOM CLASS


class EscapeRoom
{
    private $player;
    private $puzzles = [];
    private $finalCode;
    private $gameOver = false;
    private $escaped = false;
    private $maxAttempts = 5;

    public function __construct($player, $finalCode)
    {
        $this->player = $player;
        $this->finalCode = $finalCode;
    }

    // Add puzzle
    public function addPuzzle($puzzle)
    {
        $this->puzzles[] = $puzzle;
    }

    // Display welcome message
    public function welcome()
    {
        echo "\n";
        echo "============================================\n";
        echo "          WELCOME TO ESCAPE ROOM\n";
        echo "============================================\n";

        echo "Player: " . $this->player->getName() . "\n";

        echo "\nYou are locked inside a mysterious room.\n";
        echo "Solve the puzzles and find the final code.\n";
        echo "You have only " . $this->maxAttempts . " wrong attempts.\n";

        echo "============================================\n";
    }

    // Show available puzzles
    public function showPuzzles()
    {
        echo "\n========== PUZZLES ==========\n";

        foreach ($this->puzzles as $index => $puzzle) {

            $number = $index + 1;

            if ($puzzle->isSolved()) {
                echo "$number. " . $puzzle->getName() . " [SOLVED]\n";
            } else {
                echo "$number. " . $puzzle->getName() . " [UNSOLVED]\n";
            }
        }

        echo "=============================\n";
    }

    // Solve a puzzle
    public function solvePuzzle($number)
    {
        $index = $number - 1;

        // Check whether puzzle exists
        if (!isset($this->puzzles[$index])) {

            echo "\nInvalid puzzle number.\n";

            return;
        }

        $puzzle = $this->puzzles[$index];

        // Check if already solved
        if ($puzzle->isSolved()) {

            echo "\nThis puzzle is already solved.\n";

            return;
        }

        // Display puzzle
        $puzzle->showPuzzle();

        echo "\nEnter your answer.\n";
        echo "Type HINT if you need a hint.\n";

        $answer = readline("Your answer: ");

        // Hint
        if (strtolower(trim($answer)) == "hint") {

            $puzzle->showHint();

            $answer = readline("Your answer: ");
        }

        // Check answer
        if ($puzzle->checkAnswer($answer)) {

            echo "\nCorrect answer!\n";
            echo "You solved: " . $puzzle->getName() . "\n";

            $this->player->addScore(20);

            // Give rewards
            if ($number == 1) {

                echo "\nYou found a KEY!\n";

                $this->player->addItem("Key");

            } elseif ($number == 2) {

                echo "\nYou found a CODE CLUE!\n";

                $this->player->addItem("Code Clue");

            } elseif ($number == 3) {

                echo "\nYou found the final code clue!\n";

                $this->player->addItem("Final Clue");
            }

        } else {

            echo "\nWrong answer!\n";

            $this->player->increaseAttempt();

            $remaining =
                $this->maxAttempts -
                $this->player->getAttempts();

            echo "Wrong attempts: "
                . $this->player->getAttempts()
                . "/"
                . $this->maxAttempts
                . "\n";

            echo "Remaining attempts: $remaining\n";

            $this->player->addScore(-5);

            // Game over
            if ($this->player->getAttempts() >= $this->maxAttempts) {

                $this->gameOver = true;

                echo "\n====================================\n";
                echo "              GAME OVER\n";
                echo "====================================\n";

                echo "You used all your attempts.\n";
            }
        }
    }

    // Check whether all puzzles are solved
    public function allPuzzlesSolved()
    {
        foreach ($this->puzzles as $puzzle) {

            if (!$puzzle->isSolved()) {
                return false;
            }
        }

        return true;
    }

    // Final door
    public function openFinalDoor()
    {
        if ($this->gameOver) {

            echo "\nGame is already over.\n";

            return;
        }

        // Check all puzzles
        if (!$this->allPuzzlesSolved()) {

            echo "\nYou cannot open the final door yet.\n";
            echo "You must solve all puzzles first.\n";

            return;
        }

        // Check required items
        if (!$this->player->hasItem("Key")) {

            echo "\nYou don't have the required key.\n";

            return;
        }

        echo "\n====================================\n";
        echo "          FINAL DOOR\n";
        echo "====================================\n";

        echo "All puzzles are solved!\n";
        echo "You have the key.\n";

        $code = readline("\nEnter the final code: ");

        if ($code == $this->finalCode) {

            $this->escaped = true;

            $this->player->addScore(50);

            echo "\n====================================\n";
            echo "          CONGRATULATIONS!\n";
            echo "====================================\n";

            echo "The code is correct!\n";
            echo "The door is opening...\n";
            echo "You escaped from the room!\n";

            echo "\nFinal Score: "
                . $this->player->getScore()
                . "\n";

            echo "====================================\n";
        } else {

            echo "\nWrong final code!\n";

            $this->player->increaseAttempt();

            $remaining =
                $this->maxAttempts -
                $this->player->getAttempts();

            echo "Remaining attempts: $remaining\n";

            if ($this->player->getAttempts() >= $this->maxAttempts) {

                $this->gameOver = true;

                echo "\n====================================\n";
                echo "              GAME OVER\n";
                echo "====================================\n";
            }
        }
    }

    // Display current game status
    public function showStatus()
    {
        echo "\n========== GAME STATUS ==========\n";

        echo "Player: "
            . $this->player->getName()
            . "\n";

        echo "Score: "
            . $this->player->getScore()
            . "\n";

        echo "Wrong Attempts: "
            . $this->player->getAttempts()
            . "/"
            . $this->maxAttempts
            . "\n";

        echo "Puzzles Solved: ";

        $count = 0;

        foreach ($this->puzzles as $puzzle) {

            if ($puzzle->isSolved()) {
                $count++;
            }
        }

        echo $count . "/" . count($this->puzzles) . "\n";

        echo "=================================\n";
    }

    // Start the game
    public function start()
    {
        $this->welcome();

        while (!$this->gameOver && !$this->escaped) {

            echo "\n";
            echo "============== MENU ==============\n";
            echo "1. View Puzzles\n";
            echo "2. Solve Puzzle\n";
            echo "3. View Inventory\n";
            echo "4. View Status\n";
            echo "5. Open Final Door\n";
            echo "6. Exit Game\n";
            echo "==================================\n";

            $choice = readline("Enter your choice: ");

            switch ($choice) {

                case 1:

                    $this->showPuzzles();

                    break;

                case 2:

                    $this->showPuzzles();

                    $number =
                        readline("\nEnter puzzle number: ");

                    $this->solvePuzzle((int)$number);

                    break;

                case 3:

                    $this->player->showInventory();

                    break;

                case 4:

                    $this->showStatus();

                    break;

                case 5:

                    $this->openFinalDoor();

                    break;

                case 6:

                    echo "\nYou left the game.\n";

                    $this->gameOver = true;

                    break;

                default:

                    echo "\nInvalid choice.\n";
            }
        }

        echo "\nThank you for playing!\n";
    }
}


// 4. CREATE PLAYER


echo "Enter your name: ";

$name = readline();

$player = new Player($name);


// 5. CREATE PUZZLES



// Puzzle 1
$puzzle1 = new Puzzle(
    "Number Puzzle",
    "I am an even number.\n"
    . "I am greater than 10.\n"
    . "I am less than 20.\n"
    . "What number am I?",
    "14",
    "I am greater than 13 and less than 15."
);


// Puzzle 2
$puzzle2 = new Puzzle(
    "Word Puzzle",
    "Unscramble this word:\n"
    . "ROOD\n"
    . "What word am I?",
    "DOOR",
    "You use this to enter or leave a room."
);


// Puzzle 3
$puzzle3 = new Puzzle(
    "Mathematical Puzzle",
    "What is the result of:\n"
    . "6 × 7?",
    "42",
    "Think about multiplication."
);


// 6. CREATE ESCAPE ROOM


$game = new EscapeRoom(
    $player,
    "472"
);


// 7. ADD PUZZLES TO GAME

$game->addPuzzle($puzzle1);
$game->addPuzzle($puzzle2);
$game->addPuzzle($puzzle3);


// 8. START GAME

$game->start();

?>