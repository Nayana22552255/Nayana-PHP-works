<?php

class Board
{
    private $board;

    // Constructor
    public function __construct()
    {
        $this->initializeBoard();
    }

    // Initialize the board
    public function initializeBoard()
    {
        $this->board = [
            1 => " ",
            2 => " ",
            3 => " ",
            4 => " ",
            5 => " ",
            6 => " ",
            7 => " ",
            8 => " ",
            9 => " "
        ];
    }

    // Display the board
    public function displayBoard()
    {
        echo "\n";
        echo " " . $this->board[1] . " | " . $this->board[2] . " | " . $this->board[3] . "\n";
        echo "---+---+---\n";
        echo " " . $this->board[4] . " | " . $this->board[5] . " | " . $this->board[6] . "\n";
        echo "---+---+---\n";
        echo " " . $this->board[7] . " | " . $this->board[8] . " | " . $this->board[9] . "\n";
        echo "\n";
    }

    // Check whether a position is available
    public function isPositionAvailable($position)
    {
        if (!isset($this->board[$position])) {
            return false;
        }

        return $this->board[$position] === " ";
    }

    // Place a symbol on the board
    public function placeSymbol($position, $symbol)
    {
        if ($this->isPositionAvailable($position)) {
            $this->board[$position] = $symbol;
            return true;
        }

        return false;
    }

    // Check whether the given symbol has won
    public function isWinner($symbol)
    {
        $winningPositions = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],

            [1, 4, 7],
            [2, 5, 8],
            [3, 6, 9],

            [1, 5, 9],
            [3, 5, 7]
        ];

        foreach ($winningPositions as $position) {

            if (
                $this->board[$position[0]] === $symbol &&
                $this->board[$position[1]] === $symbol &&
                $this->board[$position[2]] === $symbol
            ) {
                return true;
            }
        }

        return false;
    }

    // Check whether the board is full
    public function isFull()
    {
        foreach ($this->board as $position) {

            if ($position === " ") {
                return false;
            }
        }

        return true;
    }
}

?>