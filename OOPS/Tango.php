<?php

class TangoGame
{
    private $size=6;
    private $board=[];
    private $links=[];


    public function __construct()
    {
                for($row=0; $row< $this->size; $row++)
                    {
                        $this->board[$row]= array_fill(0, $this->size, "-");
                    }
                    $this->links=[
                        [0,0,0,1,"="],
                        [1,2,2,2,"X"],
                        [3,4,4,4,"="]
                    ];
    }

    private function getBoardCellDisplay($row, $col)
    {
        if ($this->board[$row][$col] !== "-")
        {
            return $this->board[$row][$col];
        }

        if ($row === 0 && $col === 0)
        {
            return "-";
        }

        foreach ($this->links as $link)
        {
            [$row1, $col1, $row2, $col2, $type] = $link;

            if (($row1 === $row && $col1 === $col) || ($row2 === $row && $col2 === $col))
            {
                return $type;
            }
        }

        return "-";
    }

    public function displayBoard()
    {
        echo "\n  1 2 3 4 5 6 \n";
        echo "------------------\n";

        for($row=0; $row < $this->size; $row++)
            {
                echo ($row +1). "|";

                for($col=0; $col < $this->size; $col++)
                    {
                        echo $this->getBoardCellDisplay($row, $col). " ";
                    }
                echo "\n";
            }

        echo "\n Special links (Coordinates are 1 based): \n";

        foreach($this->links as $link)
            {
                echo "(". ($link[0]+1). "," . 
                ($link[1]+1). ")". $link[4]. "(". 
                ($link[2]+1). ",". 
                ($link[3]+1). ")\n";
            }

    }

    private function isValid($row,$col,$symbol)
    {
            $previousSymbol = $this->board[$row][$col];
            $this->board[$row][$col] = $symbol;

            $valid= $this->checkRows($row) && $this->checkColumn($col) && $this->checkLinks();

            $this->board[$row][$col] = $previousSymbol;
            return $valid;
    }

    private function checkRows($row)
    {
        $sunCount = 0;
        $moonCount = 0;

        for ($col = 0; $col < $this->size; $col++)
        {
            $value = $this->board[$row][$col];

            if ($value === "S")
            {
                $sunCount++;
            }
            elseif ($value === "M")
            {
                $moonCount++;
            }
            if ($col >= 2 &&
                $value !== "-" &&
                $value === $this->board[$row][$col - 1] &&
                $value === $this->board[$row][$col - 2])
            {
                return false;
            }

            if ($sunCount > 3 || $moonCount > 3)
            {
                return false;
            }
        }

        if (!in_array("-", $this->board[$row], true))
        {
            return $sunCount === 3 && $moonCount === 3;
        }

        return true;
    }

    private function checkColumn($col)
    {
        $sunCount = 0;
        $moonCount = 0;

        for ($row = 0; $row < $this->size; $row++)
        {
            $value = $this->board[$row][$col];

            if ($value === "S")
            {
                $sunCount++;
            }
            elseif ($value === "M")
            {
                $moonCount++;
            }
            if ($row >= 2 &&
                $value !== "-" &&
                $value === $this->board[$row - 1][$col] &&
                $value === $this->board[$row - 2][$col])
            {
                return false;
            }

            if ($sunCount > 3 || $moonCount > 3)
            {
                return false;
            }
        }

        $complete = true;
        for ($row = 0; $row < $this->size; $row++)
        {
            if ($this->board[$row][$col] === "-")
            {
                $complete = false;
                break;
            }
        }

        if ($complete)
        {
            return $sunCount === 3 && $moonCount === 3;
        }

        return true;
    }

    private function checkLinks()
    {
        foreach($this->links as $link)
            {
                [$row1,$col1,$row2,$col2, $type]= $link;

                $first=$this->board[$row1][$col1];
                $second=$this->board[$row2][$col2];

                if($first === "-" || $second === "-")
                    {
                        continue;
                    }
                if($type === "=" && $first !== $second)
                    {
                        return false;
                    }
                if($type === "X" && $first === $second)
                    {
                        return false;
                    }
            }

            return true;
    }

    public function makeMove($row,$col, $symbol)
    {
            $row--;
            $col--;
            $symbol = strtoupper($symbol);

            if($row <0 || $row >= $this->size ||
            $col < 0 || $col >= $this->size)
            {
                echo "Invalid row or column\n";
                return false;
            }

        if(!in_array($symbol, ["S", "M"], true))
        {
            echo "Symbol must be S or M.\n";
            return false;
        }

        if(!$this->isValid($row,$col,$symbol))
            {
                echo "Invalid move! check the tango rules.\n";
                return false;
            }

        $this->board[$row][$col]= $symbol;
        echo "Move accepted!\n";
        return true;
    }

    public function checkWin()
    {
        foreach($this->board as $row)
            {
                if(in_array("-",$row,true))
                    {
                        return false;
                    }
            }

        for($i=0; $i < $this->size; $i++)
            {
                if(!$this->checkRows($i))
                    {
                        return false;
                    }
                if(!$this->checkColumn($i))
                    {
                        return false;
                    }
            }

            return $this->checkLinks();
    }

    public function play()
    {
        echo "---------------------Tango game------------------\n";
        echo "S= Sun. M= Moon, -= Empty\n";
        echo "On the board, = means linked cells must match; X means they must differ.\n";
        echo "Enter S or M in a linked cell to replace its displayed marker.\n";
        echo "Enter Row column symbols,e.g. 1 1 S\n";
        echo "Enter Q to Quit\n";


        while(true)
            {
                $this->displayBoard();

                if($this->checkWin())
                    {
                        echo "Congratulations! puzzle completed\n";
                        break;
                    }

                $input = trim(readline("Your move:"));

                if (strtoupper($input) === "Q")
                {
                    echo "Game ended.\n";
                    break;
                }

                $parts = preg_split('/\s+/', trim($input));

                if (
                    count($parts) !== 3 ||
                    !ctype_digit($parts[0]) ||
                    !ctype_digit($parts[1]) ||
                    !in_array(strtoupper($parts[2]), ["S", "M"], true)
                )
                {
                    echo "Format: row column S/M \n";
                    continue;
                }

                $this->makeMove((int)$parts[0], (int)$parts[1], $parts[2]);
            }
    }
}

$game=new TangoGame();
$game->play();


?>