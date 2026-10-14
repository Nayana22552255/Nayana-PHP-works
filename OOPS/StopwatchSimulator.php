<?php
class Stopwatch
{
    private ?int $startTime = null;
    private int $elapsedTime = 0;
    private bool $running = false;

    public function start(): void
    {
        if ($this->running) {
            echo "Stopwatch is already running\n";
            return;
        }

        $this->startTime = hrtime(true);
        $this->running = true;
        echo "Stopwatch started\n";
    }

    public function stop(): void
    {
        if (!$this->running) {
            echo "Stopwatch is not running\n";
            return;
        }

        $this->elapsedTime += hrtime(true) - $this->startTime;
        $this->startTime = null;
        $this->running = false;
        echo "Stopwatch is stopped\n";
    }

    public function reset(): void
    {
        $this->startTime = null;
        $this->elapsedTime = 0;
        $this->running = false;
        echo "Stopwatch is resetted\n";
    }

    public function getElapsedTime(): int
    {
        $totalElapsed = $this->elapsedTime;

        if ($this->running && $this->startTime !== null) {
            $totalElapsed += hrtime(true) - $this->startTime;
        }

        return $totalElapsed;
    }

    public function displayTime(): void
    {
        $totalNanoseconds = $this->getElapsedTime();
        $totalSeconds = intdiv($totalNanoseconds, 1_000_000_000);

        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        echo sprintf("Elapsed Time: %02d:%02d:%02d\n", $hours, $minutes, $seconds);
    }
}

$stopwatch = new Stopwatch();
$stopwatch->start();

sleep(2);

$stopwatch->displayTime();

$stopwatch->stop();

$stopwatch->displayTime();
?>