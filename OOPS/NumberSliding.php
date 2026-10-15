<?php

class NumberSliding
{
        private $board=[];
        private $size;
        private $moves=0;


        public function __construct($size=4)
        {
            $this->size=$size;
            $this->initializeBoard();
            $this->shuffleBoard();
        }

        private function initializeBoard()
        {
                $number=1;

                for($row=0; $row < $this->size; $row++)
                    {

                            for($col=0; $col< $this->size; $col++)
                                {
                                        if($number < $this->size * $this->size)
                                            {
                                                $this->board[$row][$col]=$number;
                                                $number++;
                                            }
                                        else
                                            {
                                                $this->board[$row][$col]=0;
                                            }
                                }
                    }
        }

        public function findEmptySpace()
        {
                for($row=0; $row < $this->size; $row++)
                    {
                        For($col=0; $col < $this->size; $col++)
                        {
                            if($this->board[$row][$col] === 0)
                                {
                                    return [$row, $col];
                                }
                        }
                    }

                    return [-1,-1];         //fallback if no black space is found
        }

        public function displayBoard()
        {
           echo "\n";

           for($row=0; $row< $this->size; $row++)
            {
                for($col=0; $col< $this->size; $col++)
                    {
                        if($this->board[$row][$col] === 0)
                            {
                                printf("| %2s", " ");
                            }
                            else
                                {
                                    printf("| %2d", $this->board[$row][$col]);
                                }
                    }
                     echo "|\n";
                    echo str_repeat("----", $this->size). "-\n";
                }
                
                echo "Moves: ".$this->moves. "\n";

        }

        public function moveTile( int $number)
        {
            if($number < 1 || $number >=$this->size * $this->size)
                {
                    echo "Invalid Number!\n";
                    return false;
                }

            [$emptyRow, $emptyCol]= $this->findEmptySpace();

            for($row=0; $row < $this->size; $row++)
                {
                    for($col=0; $col< $this->size; $col++)
                        {
                            if($this-> board[$row][$col] === $number)
                                {
                                   $distance= abs($row -$emptyRow) + abs($col -$emptyCol);
                                     if($distance !== 1)
                                        {
                                                echo "Invalid Move!\n";
                                                return false;
                                        }

                                     // swapping the tile and black space
                                    $this->board[$emptyRow][$emptyCol] = $this->board[$row][$col];
                                    $this->board[$row][$col]=0;
                                    $this->moves++;
                                    return true;

                                }
                        }
                }

          return false;
                
        }

        public function shuffleBoard()
        {
             $previousEmptyPosition = null;

             for ($i = 0; $i < 200; $i++)
             {
                    [$emptyRow, $emptyCol] = $this->findEmptySpace();
                    $possibleMoves = [];

                    $directions = [
                        [-1, 0], // up
                        [1, 0],  // down
                        [0, -1], // left
                        [0, 1],  // right
                    ];

                    foreach ($directions as [$directionRow, $directionCol])
                    {
                            $newRow = $emptyRow + $directionRow;
                            $newCol = $emptyCol + $directionCol;

                            if (
                                $newRow >= 0 &&
                                $newRow < $this->size &&
                                $newCol >= 0 &&
                                $newCol < $this->size &&
                                !( $previousEmptyPosition !== null &&
                                   $newRow === $previousEmptyPosition[0] &&
                                   $newCol === $previousEmptyPosition[1])
                            ) {
                                    $possibleMoves[] = [$newRow, $newCol];
                            }
                    }

                    if (empty($possibleMoves)) {
                            break;
                    }

                    [$newRow, $newCol] = $possibleMoves[array_rand($possibleMoves)];
                    $previousEmptyPosition = [$emptyRow, $emptyCol];

                    $this->board[$emptyRow][$emptyCol] = $this->board[$newRow][$newCol];
                    $this->board[$newRow][$newCol] = 0;
             }
        }

        public function isSolved()
        {

            for($row=0; $row <$this->size; $row++)
                {
                    for($col=0; $col< $this->size; $col++)
                        {
                                 if($row === $this->size -1 && $col === $this->size -1)
                                    {
                                        return $this->board[$row][$col] ===0;
                                    }
                                 if($this->board[$row][$col] !== $expected)
                                    {
                                        return false;
                                    }

                                $expected++;
                        }
                }

                return true;

        }

        public function play()
        {
            
                echo "====== NUMBER SLIDING GAME=====\n";
                echo "Arrange the numbers in the ascending order.\n";
                echo "Enter a number beside the blank space to move it.\n" ;
                echo "Enter q to quit the game \n";
        
        
        
            while(true)
                {
                    $this->displayBoard();

                    if($this->isSolved())
                        {
                            echo "Congratulations! You solved the puzzle\n";
                            echo "Total moves: ".$this->moves. "\n";
                             break;
                        }

                    $input=trim(readline("enter the tile number to move: "));

                    if(strtolower($input)=== "q")
                        {
                            echo "Game ended. Thanks for playing\n";
                            break;
                        }

                    if(!ctype_digit($input))
                        {
                            echo "please enter a valid positive number. \n";
                            continue;
                        }

                    $this->moveTile($input);
                }
        }
}

$game= new NumberSliding();
$game->play();

?>