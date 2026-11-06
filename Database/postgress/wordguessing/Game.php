
<?php

class Game
{
    private string $word;
    private string $hint;
    private int $wordId;
    private string $difficulty;

    private $attempts = 6;
    private $guessedLetters = [];

    public function __construct(array $wordData)
    {
        $this->word = strtolower($wordData['word']);
        $this->hint = $wordData['hint'];
        $this->wordId = $wordData['word_id'];
        $this->difficulty = $wordData['difficulty'];
    }

    // Start game
    public function start()
    {
        echo "\n========== GAME STARTED ==========\n";

        echo "Hint: " . $this->hint . "\n";
        echo "Difficulty: " . ucfirst($this->difficulty) . "\n";

        while ($this->attempts > 0) {

            $display = "";

            foreach (str_split($this->word) as $letter) {

                if (in_array($letter, $this->guessedLetters)) {
                    $display .= $letter . " ";
                } else {
                    $display .= "_ ";
                }
            }

            echo "\nWord: " . $display;
            echo "\nAttempts remaining: " . $this->attempts;

            // Check whether the player has won
            if (!str_contains($display, "_")) {

                echo "\n\nCongratulations! You won!\n";

                return true;
            }

            echo "\nGuessed letters: ";

            if (empty($this->guessedLetters)) {
                echo "None";
            } else {
                echo implode(", ", $this->guessedLetters);
            }

            echo "\nEnter a letter: ";

            $guess = strtolower(trim(readline()));

            // Validate input
            if (strlen($guess) != 1 || !ctype_alpha($guess)) {
                echo "Please enter only one alphabet letter!\n";
                continue;
            }

            // Check repeated letter
            if (in_array($guess, $this->guessedLetters)) {
                echo "You already guessed this letter!\n";
                continue;
            }

            $this->guessedLetters[] = $guess;

            // Check correct or incorrect guess
            if (str_contains($this->word, $guess)) {

                echo "Correct guess!\n";

            } else {

                echo "Wrong guess!\n";

                $this->attempts--;
            }
        }

        echo "\nGame Over!";
        echo "\nCorrect word: " . $this->word . "\n";

        return false;
    }

    // Get word ID
    public function getWordId()
    {
        return $this->wordId;
    }

    // Calculate score
    public function getScore()
    {
        if ($this->difficulty == "easy") {
            $baseScore = 10;
        } elseif ($this->difficulty == "medium") {
            $baseScore = 20;
        } else {
            $baseScore = 30;
        }

        return $baseScore * $this->attempts;
    }
}
