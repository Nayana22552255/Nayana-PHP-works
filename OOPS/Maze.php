<?php 

class Maze 
{
    private $width;
    private $height;
    private $maze = [];
    private $playerX;
    private $playerY;
    private $attempts = 0;
    private $moves = 0;

    public function __construct($width, $height) 
    {
        $this->width = $width;
        $this->height = $height;

        // $this->generateMaze();

        do{
            $this->attempts++;
            $this->generateMaze();
        }while (!$this->hasPath());

        echo "Maze generated in " . $this->attempts . "attempt(s)." . "\n";

        $this->playerX = 1;
        $this->playerY = 1;

    }

    private function generateMaze()
    {
        for ($y = 0; $y < $this->height; $y++)   //row
            {
                for ($x = 0; $x < $this->width; $x++){  //column
                    if (
                        $x == 0 ||
                        $y == 0 ||
                        $x == $this->width - 1 ||
                        $y == $this->height - 1
                    ){
                        $this->maze[$y][$x] = '#';
                    }else{
                        //create a path randomly
                        $this->maze[$y][$x] = (rand(1, 100) <= 50)? '#' : ' ';

                    }
                }
            }

            //Start
            $this->maze[1][1] = 'S';

            //End

            $this->maze[$this->height - 2][$this->width - 2] = 'E';
    }

    public function display()
    {
        for ($y = 0; $y < $this->height; $y++){
            for ($x = 0; $x < $this->width; $x++){
                if ($x == $this->playerX && $y == $this->playerY){
                    echo "P";
                }else{
                    echo $this->maze[$y][$x];
                }
            }
              echo "\n";
        }
      
        // foreach ($this->maze as $row){
        //     echo implode('',$row) . "\n";
        // }
    }
    public function hasPath()
    {
        $queue = [[1, 1]];
        $visited = [];

        $visited[1][1] = true;

        $directions = [
            [1, 0],
            [-1, 0],
            [0, 1],
            [0, -1]
        ];
        while (!empty($queue)){
            $current = array_shift($queue);

            $x = $current[0];
            $y = $current[1];

            if ( $x == $this->width - 2 && $y == $this->height - 2)
                {
                    return true;
                }
                foreach ($directions as $direction){
                    $nx = $x + $direction[0];
                    $ny = $y + $direction[1];

                    if (
                        $nx >= 0 && $nx < $this->width && $ny >= 0 &&
                        $ny < $this->height && !isset($visited[$ny][$nx]) && $this->maze[$ny][$nx] != '#'
                    ){
                        $visited[$ny][$nx]  = true;

                        $queue[] = [$nx, $ny];
                    }
                }
        }
        return false;
    }

    public function movePlayer($direction)
    {
        $newX = $this->playerX;
        $newY = $this->playerY;

        switch($direction){
            case 'w' : 
                $newY--;            //Move up
                break;
            case 's' :
                $newY++;            //move down
                break;
            case 'a':
                $newX--;        //move left
                break;
            case 'd':
                $newX++;        //move right
                break;
            default : 
                echo "Invalid movement. Use W, A, S or D." . "\n";
                return;
        }

        if ($this->maze[$newY][$newX] != '#'){
            $this->playerX = $newX;
            $this->playerY = $newY;
            $this->moves++;
        } else {
            echo "You cannot move through a wall". "\n";
        }
    }
    public function hasWon()
    {
        return ($this->playerX == $this->width - 2 && 
        $this->playerY == $this->height - 2);
    }

    public function getMoves()
    {
        return $this->moves;
    }

    public function resetGame()
    {
        $this->attempts = 0;
        $this->moves = 0;

        do {
            $this->attempts++;
            $this->generateMaze();
        }while(!$this->hasPath());

        $this->playerX = 1;
        $this->playerY = 1;
    }
}


$maze = new Maze(20,10);
while(true){
    $maze->display();
    echo "Moves : " . $maze->getMoves() . "\n";
    // echo "Enter move (W/A/S/D): ";
    $move = readline("Enter the move (W/A/S/D)");

    $maze->movePlayer($move);

    if ($maze->hasWon()){
        $maze->display();

        echo "\n";
        echo "Congratulations! You reached the end!"  . "\n";
        echo "Total moves : " . $maze->getMoves() . "\n";
        echo "Game Over!" . "\n";

        // break;
        echo "\n";
        echo "1. Play Again ". "\n";
        echo "2. Exit" ."\n";

        $choice = readline("Choose an option: ");

        if ($choice == '1')
            {
                $maze ->resetGame();
            }elseif ($choice =='2')
            {
                echo "Thanks for playing" . "\n";
                break;
            }else{
                echo "Invalid option " . "\n";
                break;
            }

    }

    echo "\n";
}

?>