<?php

class DigitalClock
{
    private $format;

    
    public function __construct($format)
    {
        $this->format = $format;
    }

    public function getCurrentTime()
    {
        
        date_default_timezone_set("Asia/Kolkata");
        if ($this->format == 24)
        {
            return date("H:i:s");
        }
        else
        {
            return date("h:i:s A");
        }
    }

    public function displayTime()
    {
        echo "=========================\n";
        echo "       Digital Clock\n";
        echo "=========================\n";
        echo "       " . $this->getCurrentTime() . "\n";
        echo "=========================\n";
    }

    public function start()
    {
        while (true)
        {
            system("cls");

            $this->displayTime();

            sleep(1);
        }
    }
}

$clock = new DigitalClock(24);

$clock->start();

?>