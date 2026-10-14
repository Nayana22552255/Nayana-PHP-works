
<?php

class Ball
{
    public $x;
    public $y;
    public $dx = 1;
    public $dy = -1;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function reset()
    {
        $this->x = 10;
        $this->y = 7;
        $this->dx = 1;
        $this->dy = -1;
    }
}

class Paddle
{
    public $x;
    public $width = 5;
    public $y = 8;

    public function __construct()
    {
        $this->x = 8;
    }

    public function move($direction)
    {
        if ($direction == "a" && $this->x > 0) {
            $this->x--;
        } elseif ($direction == "d" && $this->x + $this->width < 20) {
            $this->x++;
        }
    }
}

class Brick
{
    public $bricks = [];

    public function __construct()
    {
        for ($y = 1; $y <= 3; $y++) {
            for ($x = 2; $x <= 17; $x += 2) {
                $this->bricks["$x,$y"] = true;
            }
        }
    }

    public function hasBrick($x, $y)
    {
        return isset($this->bricks["$x,$y"]);
    }

    public function removeBrick($x, $y)
    {
        unset($this->bricks["$x,$y"]);
    }

    public function countBricks()
    {
        return count($this->bricks);
    }
}

class BreakoutGame
{
    private $width = 20;
    private $height = 10;
    private $ball;
    private $paddle;
    private $brick;
    private $score = 0;
    private $lives = 3;

    public function __construct()
    {
        $this->ball = new Ball(10, 7);
        $this->paddle = new Paddle();
        $this->brick = new Brick();
    }

    public function displayBoard()
    {
        echo "\n";

        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                if ($x == $this->ball->x && $y == $this->ball->y) {
                    echo "O";
                } elseif (
                    $y == $this->paddle->y &&
                    $x >= $this->paddle->x &&
                    $x < $this->paddle->x + $this->paddle->width
                ) {
                    echo "=";
                } elseif ($this->brick->hasBrick($x, $y)) {
                    echo "#";
                } else {
                    echo " ";
                }
            }

            echo "\n";
        }

        echo "Score: " . $this->score . "\n";
        echo "Lives: " . $this->lives . "\n";
        echo "Bricks remaining: " . $this->brick->countBricks() . "\n";
    }

    public function update($direction)
    {
        $this->paddle->move($direction);

        $nextX = $this->ball->x + $this->ball->dx;
        $nextY = $this->ball->y + $this->ball->dy;

        if ($nextX < 0) {
            $this->ball->dx = 1;
            $nextX = 0;
        } elseif ($nextX >= $this->width) {
            $this->ball->dx = -1;
            $nextX = $this->width - 1;
        }

        if ($nextY < 0) {
            $this->ball->dy = 1;
            $nextY = 0;
        }

        if ($this->brick->hasBrick($nextX, $nextY)) {
            $this->brick->removeBrick($nextX, $nextY);
            $this->score += 10;
            $this->ball->dy *= -1;
            $nextY = $this->ball->y + $this->ball->dy;
        }

        if (
            $nextY == $this->paddle->y &&
            $nextX >= $this->paddle->x &&
            $nextX < $this->paddle->x + $this->paddle->width
        ) {
            $this->ball->dy = -1;
            $nextY = $this->ball->y + $this->ball->dy;
        }

        if ($nextY >= $this->height) {
            $this->lives--;
            $this->ball->reset();
            return;
        }

        $this->ball->x = $nextX;
        $this->ball->y = $nextY;
    }

    public function play()
    {
        echo "===== BREAKOUT GAME =====\n";
        echo "Controls: A = Left, D = Right, S = Stay\n";
        echo "Press Q to quit.\n";

        while ($this->lives > 0 && $this->brick->countBricks() > 0) {
            $this->displayBoard();

            $input = strtolower(trim(readline("Enter your move (A/D/S/Q): ")));

            if ($input == "q") {
                echo "Game exited.\n";
                return;
            }

            if (!in_array($input, ["a", "d", "s"])) {
                echo "Invalid input. Try A, D, or S.\n";
                continue;
            }

            $this->update($input);
        }

        $this->displayBoard();

        if ($this->brick->countBricks() == 0) {
            echo "Congratulations! You broke all the bricks!\n";
        } else {
            echo "Game Over! You ran out of lives.\n";
        }

        echo "Final score: " . $this->score . "\n";
    }
}

    $game = new BreakoutGame();
    $game->play();


