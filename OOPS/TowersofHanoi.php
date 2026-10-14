<?php

class TowerOfHanoi
{
    private $totalDisks;
    private $towers = [
        "A" => [],
        "B" => [],
        "C" => []
    ];
    private $moves = 0;

    public function __construct($disks)
    {
        if (!is_numeric($disks) || (int) $disks != $disks || (int) $disks < 1) {
            throw new InvalidArgumentException("Number of disks must be a positive integer.");
        }

        $this->totalDisks = (int) $disks;

        for ($i = $this->totalDisks; $i >= 1; $i--) {
            $this->towers["A"][] = $i;
        }
    }

    public function displayTowers()
    {
        echo "\nTower A: ";
        print_r($this->towers["A"]);

        echo "\nTower B: ";
        print_r($this->towers["B"]);

        echo "\nTower C: ";
        print_r($this->towers["C"]);

        echo "\nTotal moves: " . $this->moves . "\n";
    }

    public function moveDisk($source, $destination)
    {
        $source = strtoupper(trim($source));
        $destination = strtoupper(trim($destination));

        if (!isset($this->towers[$source]) || !isset($this->towers[$destination]) || $source === $destination) {
            echo "Invalid tower selection.\n";
            return false;
        }

        if (empty($this->towers[$source])) {
            echo "Source tower is empty.\n";
            return false;
        }

        $disk = end($this->towers[$source]);

        if (!empty($this->towers[$destination])) {
            $topDisk = end($this->towers[$destination]);

            if ($disk > $topDisk) {
                echo "Invalid move! A larger disk cannot be placed on a smaller one.\n";
                return false;
            }
        }

        array_pop($this->towers[$source]);
        $this->towers[$destination][] = $disk;
        $this->moves++;

        echo "Moved disk $disk from $source to $destination.\n";
        return true;
    }

    public function isCompleted()
    {
        return count($this->towers["C"]) === $this->totalDisks;
    }

    public function play()
    {
        echo "\n===========================\n";
        echo "Towers of Hanoi\n";
        echo "===========================\n";

        while (!$this->isCompleted()) {
            $this->displayTowers();

            $source = readline("Enter source tower (A/B/C): ");
            if ($source === false || trim($source) === "") {
                echo "Please enter a valid tower.\n";
                continue;
            }

            $destination = readline("Enter destination tower (A/B/C): ");
            if ($destination === false || trim($destination) === "") {
                echo "Please enter a valid tower.\n";
                continue;
            }

            $this->moveDisk($source, $destination);
        }

        $this->displayTowers();
        echo "\nCongratulations! You completed the game.\n";
        echo "Total moves taken: " . $this->moves . "\n";
        echo "Minimum possible moves: " . ((int) pow(2, $this->totalDisks) - 1) . "\n";
    }
}


    $game = new TowerOfHanoi(3);
    $game->play();

