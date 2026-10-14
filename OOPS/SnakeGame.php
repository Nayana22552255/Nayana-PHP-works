<?php
class SnakeGame
{
    private $width;
    private $height;
    private $snake;
    private $food;
    private $direction;
    private $score;
    private $gameover;

    public function __construct($width = 20, $height = 10)
    {
        $this->width = $width;
        $this->height = $height;

        $this->snake = [
            ["x" => 5, "y" => 5],
            ["x" => 4, "y" => 5],
            ["x" => 3, "y" => 5]
        ];

        $this->direction = "RIGHT";
        $this->score = 0;
        $this->gameover = false;

        $this->generateFood();
    }

    public function generateFood()
    {
        do {
            $foodX = rand(0, $this->width - 1);
            $foodY = rand(0, $this->height - 1);

            $occupied = false;

            foreach ($this->snake as $part) {
                if ($part["x"] == $foodX && $part["y"] == $foodY) {
                    $occupied = true;
                    break;
                }
            }
        } while ($occupied);

        $this->food = [
            "x" => $foodX,
            "y" => $foodY
        ];
    }

    public function displayBoard()
    {
        echo "\n";

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $isHead = $this->snake[0]["x"] == $x && $this->snake[0]["y"] == $y;         //snak head
                $isFood = $this->food["x"] == $x && $this->food["y"] == $y;                 //snake food
                $isBody = false;

                foreach (array_slice($this->snake, 1) as $part) {
                    if ($part["x"] == $x && $part["y"] == $y) {
                        $isBody = true;
                        break;
                    }
                }

                if ($isHead) {
                    echo "@";               //head
                } elseif ($isFood) {
                    echo "F";               //food
                } elseif ($isBody) {
                    echo "o";               //body
                } else {
                    echo ".";               //empty space
                }
            }

            echo "\n";
        }

        echo "Score: " . $this->score . "\n";
    }

    public function changeDirection($key)
    {
        $mapping = [
            "W" => "UP",
            "S" => "DOWN",
            "A" => "LEFT",
            "D" => "RIGHT",
            "w" => "UP",
            "s" => "DOWN",
            "a" => "LEFT",
            "d" => "RIGHT",
            "UP" => "UP",
            "DOWN" => "DOWN",
            "LEFT" => "LEFT",
            "RIGHT" => "RIGHT",
        ];

        if (!isset($mapping[$key])) {
            return;
        }

        $newDirection = $mapping[$key];

        if ($this->isOppositeDirection($newDirection)) {
            return;
        }

        $this->direction = $newDirection;
    }

    private function isOppositeDirection($newDirection)
    {
        $opposites = [
            "UP" => "DOWN",
            "DOWN" => "UP",
            "LEFT" => "RIGHT",
            "RIGHT" => "LEFT",
        ];

        return isset($opposites[$this->direction]) && $opposites[$this->direction] == $newDirection;
    }

    public function move()
    {
        if ($this->gameover) {
            return;
        }

        $head = $this->snake[0];
        $nextHead = ["x" => $head["x"], "y" => $head["y"]];

        switch ($this->direction) {
            case "UP":
                $nextHead["y"]--;
                break;
            case "DOWN":
                $nextHead["y"]++;
                break;
            case "LEFT":
                $nextHead["x"]--;
                break;
            case "RIGHT":
                $nextHead["x"]++;
                break;
        }

        if ($nextHead["x"] < 0 || $nextHead["x"] >= $this->width || $nextHead["y"] < 0 || $nextHead["y"] >= $this->height) {
            $this->gameover = true;
            return;
        }

        foreach ($this->snake as $segment) {
            if ($segment["x"] == $nextHead["x"] && $segment["y"] == $nextHead["y"]) {
                $this->gameover = true;
                return;
            }
        }

        array_unshift($this->snake, $nextHead);

        if ($nextHead["x"] == $this->food["x"] && $nextHead["y"] == $this->food["y"]) {
            $this->score++;
            $this->generateFood();
        } else {
            array_pop($this->snake);
        }
    }

    public function isGameOver()
    {
        return $this->gameover;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function play()
    {
        while (!$this->gameover) {
            $this->displayBoard();
            echo "Use W/A/S/D to move or Q to quit: ";
            $input = trim(fgets(STDIN));

            if ($input === "Q" || $input === "q") {
                break;
            }

            $this->changeDirection($input);
            $this->move();
        }

        echo "Game Over! Final Score: " . $this->score . "\n";
    }
}


    $game = new SnakeGame();
    $game->play();


