
<?php

class MagicSquare
{
    private $size;
    private $square;
    private $magicsum;

    public function __construct($size)
    {
        $this->size = $size;
        $this->square = [];
        $this->magicsum = 0;
    }

    public function generateSquare()
    {
        if ($this->size < 3 || $this->size % 2 == 0) {
            throw new InvalidArgumentException(
                "This generator requires an odd size of at least 3."
            );
        }

        $this->square = array_fill(
            0,
            $this->size,
            array_fill(0, $this->size, 0)
        );

        $row = 0;
        $column = intdiv($this->size, 2);

        for (
            $number = 1;
            $number <= $this->size * $this->size;
            $number++
        ) {
            $this->square[$row][$column] = $number;

            $nextRow = ($row - 1 + $this->size)
                % $this->size;

            $nextColumn = ($column + 1)
                % $this->size;

            if ($this->square[$nextRow][$nextColumn] != 0) {
                $row = ($row + 1) % $this->size;
            } else {
                $row = $nextRow;
                $column = $nextColumn;
            }
        }

        $this->CalculateMagicSum();
    }

    public function CalculateMagicSum()
    {
        $this->magicsum =
            $this->size * (($this->size * $this->size) + 1) / 2;

        return $this->magicsum;
    }

    public function getSquare()
    {
        return $this->square;
    }

    public function getMagicsum()
    {
        return $this->magicsum;
    }

    public function DisplaySquare()
    {
        foreach ($this->square as $row) {
            echo implode(" ", $row) . "\n";
        }
    }

    public function CheckMagicSquareRows($square)
    {
        for ($i = 0; $i < $this->size; $i++) {
            $sum = 0;

            for ($j = 0; $j < $this->size; $j++) {
                $sum += $square[$i][$j];
            }

            if ($sum != $this->magicsum) {
                return false;
            }
        }

        return true;
    }

    public function CheckMagicSquareColumn($square)
    {
        for ($j = 0; $j < $this->size; $j++) {
            $sum = 0;

            for ($i = 0; $i < $this->size; $i++) {
                $sum += $square[$i][$j];
            }

            if ($sum != $this->magicsum) {
                return false;
            }
        }

        return true;
    }

    public function CheckMagicSquareDiagonal($square)
    {
        $diagonal1 = 0;
        $diagonal2 = 0;

        for ($i = 0; $i < $this->size; $i++) {
            $diagonal1 += $square[$i][$i];

            $diagonal2 +=
                $square[$i][$this->size - 1 - $i];
        }

        return (
            $diagonal1 == $this->magicsum &&
            $diagonal2 == $this->magicsum
        );
    }

    public function CompleteCheckMagicSquare($square)
    {
        // A puzzle is incomplete if any cell is missing.
        foreach ($square as $row) {
            foreach ($row as $value) {
                if ($value === null) {
                    return false;
                }
            }
        }

        return
            $this->CheckMagicSquareRows($square) &&
            $this->CheckMagicSquareColumn($square) &&
            $this->CheckMagicSquareDiagonal($square);
    }
}
?>
