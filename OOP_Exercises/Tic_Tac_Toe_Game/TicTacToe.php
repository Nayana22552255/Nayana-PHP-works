<?php

class TicTacToe
{
    private $player1;
    private $player2;
    private $board;
    private $currentPlayer;

    // Constructor
    public function __construct()
    {
        // Create players
        $this->player1 = new Player("Player 1", "X");
        $this->player2 = new Player("Player 2", "O");

        // Create board
        $this->board = new Board();

        // Set Player 1 as the first player
        $this->currentPlayer = $this->player1;
    }

    // Get the current player
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }

    // Switch between players
    public function switchPlayer()
    {
        if ($this->currentPlayer === $this->player1) {

            $this->currentPlayer = $this->player2;

        } else {

            $this->currentPlayer = $this->player1;
        }
    }

    // Start the game
    public function startGame()
    {
        while (true) {

            echo "\n";
            echo "============================\n";
            echo "       TIC TAC TOE\n";
            echo "============================\n";

            // Display board
            $this->board->displayBoard();

            // Get current player
            $player = $this->getCurrentPlayer();

            echo $player->getName() . "'s turn ";
            echo "[" . $player->getSymbol() . "]\n";

            // Get position from player
            $position = (int) readline("Enter the position (1-9): ");

            // Check position range
            if ($position < 1 || $position > 9) {

                echo "Invalid position. ";
                echo "Enter a number between 1 and 9.\n";

                continue;
            }

            // Check whether position is already occupied
            if (!$this->board->isPositionAvailable($position)) {

                echo "Position already occupied. ";
                echo "Choose another position.\n";

                continue;
            }

            // Place player's symbol
            $this->board->placeSymbol(
                $position,
                $player->getSymbol()
            );

            // Check winner
            if ($this->board->isWinner($player->getSymbol())) {

                $this->board->displayBoard();

                echo "================================\n";
                echo "Congratulations, ";
                echo $player->getName();
                echo "! You won!\n";
                echo "================================\n";

                break;
            }

            // Check draw
            if ($this->board->isFull()) {

                $this->board->displayBoard();

                echo "================================\n";
                echo "Game Draw!\n";
                echo "================================\n";

                break;
            }

            // Change player
            $this->switchPlayer();
        }
    }
}

?>