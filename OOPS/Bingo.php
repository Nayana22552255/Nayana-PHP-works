
<?php

class BingoGame
{
    private array $userCard = [];
    private array $computerCard = [];

    private const MIN_NUMBER = 1;
    private const MAX_NUMBER = 25;
    private const GRID_SIZE = 5;
    private const WINNING_LINES = 5;

    // Constructor
    public function __construct()
    {
        $this->computerCard = $this->generateComputerCard();
    }

    //  computer's random 5 x 5 grid
    private function generateComputerCard()
    {
        $numbers = range(self::MIN_NUMBER, self::MAX_NUMBER);       //used because of the properties are assigned as const

        shuffle($numbers);       //mixes the numbers in random order       

        return array_chunk(array_slice($numbers, 0, 25), 5);        //split  the aray into smaller 5 arrays
    }

    //  user's grid
    private function createUserCard()
    {
        echo "Enter 25 unique numbers between 1 and 25.\n";
        echo "Enter the numbers row by row.\n\n";

        $usedNumbers = [];

        for ($row = 0; $row < self::GRID_SIZE; $row++) {
            $this->userCard[$row] = [];

            for ($col = 0; $col < self::GRID_SIZE; $col++) {
                while (true) {
                    $input = trim(readline("Enter number for row " . ($row + 1) .", column " . ($col + 1) . ": " ));

                    if (!ctype_digit($input)) {                 //ensure that the input is only digits
                        echo "Please enter a valid whole number.\n";
                        continue;
                    }

                    $number = (int) $input;

                    if ($number < self::MIN_NUMBER || $number > self::MAX_NUMBER) {
                        echo "Number must be between 1 and 25.\n";
                        continue;
                    }

                    if (in_array($number, $usedNumbers, true)) {        //checking the duplicates
                        echo "That number is already used. Try another.\n";
                        continue;
                    }

                    $this->userCard[$row][$col] = $number;
                    $usedNumbers[] = $number;

                    break;
                }
            }
        }
    }

    // Display only the user's card
    private function displayUserCard()
    {
        echo "\n========== YOUR BINGO CARD ==========\n";
        echo "\n";
        echo "-------------------------------------\n";

        for ($row = 0; $row < self::GRID_SIZE; $row++) {
            echo "| ";

            for ($col = 0; $col < self::GRID_SIZE; $col++) {
                $value = $this->userCard[$row][$col] ?? "X";

                printf("%-5s", $value);         //for neatly arrange the bingo card &  left aligned
            }

            echo "|\n";
        }

        echo "-------------------------------------\n";
        echo " X means the number has been removed.\n";
    }

    // Get numbers still remaining on a card
    private function getRemainingNumbers( $card)
    {
        $remaining = [];

        foreach ($card as $row) {
            foreach ($row as $number) {
                if ($number !== null) {
                    $remaining[] = $number;
                }
            }
        }

        return $remaining;
    }

    // Computer automatically calls a random number
    private function computerCallNumber()
    {
        $remaining = $this->getRemainingNumbers($this->computerCard);

        if (empty($remaining)) {
            return null;
        }

        $index = array_rand($remaining);        //return a random key

        return $remaining[$index];
    }

    // Remove a selected number from both cards if present
    private function removeNumberFromBoth($number)
    {
        for ($row = 0; $row < self::GRID_SIZE; $row++) {
            for ($col = 0; $col < self::GRID_SIZE; $col++) {

                if ($this->userCard[$row][$col] === $number) {
                    $this->userCard[$row][$col] = null;
                }

                if ($this->computerCard[$row][$col] === $number) {
                    $this->computerCard[$row][$col] = null;
                }
            }
        }
    }

    // Count completed rows and columns
    private function countCompletedLines( $card)
    {
        $completed = 0;

        // Check all rows
        for ($row = 0; $row < self::GRID_SIZE; $row++) {
            $complete = true;

            for ($col = 0; $col < self::GRID_SIZE; $col++) {
                if ($card[$row][$col] !== null) {
                    $complete = false;
                    break;
                }
            }

            if ($complete) {
                $completed++;
            }
        }

        // Check all columns
        for ($col = 0; $col < self::GRID_SIZE; $col++) {
            $complete = true;

            for ($row = 0; $row < self::GRID_SIZE; $row++) {
                if ($card[$row][$col] !== null) {
                    $complete = false;
                    break;
                }
            }

            if ($complete) {
                $completed++;
            }
        }

        return $completed;
    }

    // Check the winner
    private function checkWinner()
    {
        $userLines = $this->countCompletedLines($this->userCard);
        $computerLines = $this->countCompletedLines($this->computerCard);

        echo "\nYour completed lines: $userLines\n";
        echo "Computer completed lines: $computerLines\n";

        if ($userLines >= self::WINNING_LINES && $computerLines >= self::WINNING_LINES
        ) {
            echo "\nDRAW! Both players completed five lines.\n";
            return true;
        }

        if ($userLines >= self::WINNING_LINES) {
            echo "\nBINGO! YOU WIN!\n";
            return true;
        }

        if ($computerLines >= self::WINNING_LINES) {
            echo "\nBINGO! COMPUTER WINS!\n";
            return true;
        }

        return false;
    }

    // Start the game
    public function play()
    {
        echo "========== BINGO GAME ==========\n";
        echo "Your card will be visible.\n";
        echo "The computer's card will remain hidden.\n";

        $this->createUserCard();

        echo "\nBoth cards are ready. Let the game begin!\n";

        $this->displayUserCard();

        while (true) {

            // USER'S TURN
            $remaining = $this->getRemainingNumbers($this->userCard);

            if (empty($remaining)) {
                echo "\nNo numbers remain on your card.\n";
                break;
            }

            echo "\n========== YOUR TURN ==========\n";
            echo "Your available numbers: ";
            echo implode(", ", $remaining) . "\n";   //converts an array into string

            while (true) {
                $input = trim(readline("Choose a number from your card: "));

                if (!ctype_digit($input)) {
                    echo "Please enter a valid whole number.\n";
                    continue;
                }

                $choice = (int) $input;

                if (!in_array($choice, $remaining, true)) {
                    echo "Choose a number still present on your card.\n";
                    continue;
                }

                break;
            }

            // Remove user's selected number from both cards
            $this->removeNumberFromBoth($choice);

            echo "\nYou selected: $choice\n";

            $this->displayUserCard();

            // Check winner after user's move
            if ($this->checkWinner()) {
                break;
            }

            // COMPUTER'S TURN
            echo "\n========== COMPUTER'S TURN ==========\n";

            // Computer selects a random number automatically
            $computerChoice = $this->computerCallNumber();

            if ($computerChoice === null) {
                echo "No numbers remain on the computer's card.\n";
                break;
            }

            echo "Computer called number: $computerChoice\n";

            // Remove computer's selected number from both cards
            $this->removeNumberFromBoth($computerChoice);

            $this->displayUserCard();

            // Check winner after computer's move
            if ($this->checkWinner()) {
                break;
            }

            readline("\nPress ENTER to continue to the next round...");
        }

        echo "\n========== GAME OVER ==========\n";
    }
}

// Create the game object
$game = new BingoGame();

// Start playing
$game->play();

?>
