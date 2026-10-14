<?php

class Sudoku
{
    private $board;
    private $puzzle;
    private $attempts=0;
    private $mistakes=0;

    public function __construct()
    {
            $this->puzzle=[
                [1, 0, 3, 0],
                [0, 4, 0, 2],
                [2, 0, 4, 0],
                [0, 3, 0, 1]
            ];

        $this->board=$this->puzzle;     //copy the puzzle into the board

    }
    public function DisaplayBoard()
    {
        echo "\n";
        echo "+----+-----+----+-----+\n";
        for($row =0; $row < 4; $row++)
            {
                echo "|";
                

                for($column=0; $column< 4; $column++)
                    {
                        if($this->board[$row][$column]==0)
                            {
                                echo "|";
                               
                
                            }
                            echo " ".$this->board[$row][$column]. "|";
                    }

                    echo "\n";
            }
    
        echo "+-----+----+-----+----+\n";

    }

    private function isfixedCell($row,$column)
    {
        return $this->puzzle[$row][$column] != 0 ;           //check whether thecell is part of an original puzzle
   }

   private  function isvalidNumber($number)
   {
    return $number >= 1 && $number <= 4;
   }

    private function isValidRow($row,$number)
    {
        for($column = 0; $column <4; $column++)
            {
                if($this->board[$row][$column] == $number)
                    {
                        return false;
                    }
            }

            return true;
    }

    private function isValidcolumn($column,$number)
    {
            for($row =0; $row<4; $row++)
                {
                    if($this->board[$row][$column]== $number)
                        {
                            return false;
                        }
                }
                return true;
    }

    private function isValidBox($row, $column,$number)
    {
            $startRow=floor($row / 2) * 2;

            $startcolumn=floor($column / 2) *2;

            for($i =0; $i <2; $i++)
                {
                    for($j=0; $j<2; $j++)
                        {
                            $currentRow=$startRow + $i;
                            $currentcolumn=$startcolumn + $j;


                            if($this->board[$currentRow][$currentcolumn] == $number)
                                {
                                    return false;
                                }
                        }
                }

                return true;
    }

    private function isValidMove($row, $column,$number)
    {
            if(!$this->isvalidNumber($number))
                {
                    echo "\n Invalid number! Enter a number between 1 and 4. \n";
                    return false;
                }
            if($this->isfixedCell($row,$column))
                {
                    echo "\n you cannot change a fixed number\n";
                    return false;
                }
            if(!$this->isValidRow($row,$number))
                {
                    echo "\n Invalid move!  The number already exist in this row. \n";
                    return false;
                }
            if(!$this->isValidcolumn($column,$number))
                {
                    echo "\n Invalid move! The number already exist in this column\n";
                    return false;
                }
            if(!$this->isValidBox($row,$column,$number))
                {
                    echo "\nInvalid move! the number already exist in the 2*2 box\n";
                    return false;
                }

                return true;
    }

    private function makeMove($row,$column,$number)
    {
        $this->board[$row][$column]=$number;
    }

    private function isComplete()
    {
        for($row=0; $row <4; $row++)
            {
                for($column=0; $column<4; $column++)
                    {
                        if($this->board[$row][$column] == 0)
                            {
                                return false;
                            }
                    }
            }
            return true;
    }

    private function getInput()
    {
        echo "\nenter the row(1-4):";
        $row=trim(readline());

        echo "\nenter the column(1-4)";
        $column=trim(readline());

        echo "\nenter the number(1-4)";
        $number=trim(readline());

        $row=$row - 1;
        $column=$column - 1;


        if($row <0 || $row >3 || $column <0 || $column > 3)
            {
                echo "Invalid row or column\n";
                return null;
            }

            return [
                "row"=>$row,
                "column"=>$column,
                "number"=>$number
            ];
            echo "\n";
    }

    public function play()
    {
        echo "===================================\n";
        echo "      MINI SUDOKU GAME\n";
        echo "===================================\n";


        echo "\n Rules: \n";
        echo "1.Use number 1 to 4 \n";
        echo "2.No duplicate number in a row\n";
        echo "3.No duplicate number in a column\n";
        echo "4.No duplicate number in a 2*2 Box\n";
        echo "5.You cannot change the given numbers\n";

        while(!$this->isComplete())
            {
                    $this->DisaplayBoard();

                    $input=$this->getInput();

                    if($input === null)
                        {
                            $tis->mistakes++;
                            continue;

                        }

                        $row=$input["row"];
                        $column=$input["column"];
                        $number=$input["number"];


                        $this->attempts++;


                        if($this->isValidMove($row,$column,$number))
                            {
                                $this->makeMove($row,$column,$number);
                                echo "\nCorret Move\n";
                            }
                            else
                            {
                                $this->mistakes++;
                            }
             }

            $this->DisaplayBoard();

            echo "=========================\n";
            echo "Congratualtions\n";
            echo "You solved the sudoku\n";
            echo "number of attempts: ".$this->attempts. "\n";
            echo "number of mistakes: ".$this->mistakes. "\n";
    }


}

$game= new Sudoku();

$game->play();

?>
