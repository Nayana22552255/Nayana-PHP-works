<?php

require_once("Vehicle.php");

class Car extends Vehicle
{
    private $numberOfDoors;

    public function __construct(
        $vehicleId,
        $brand,
        $model,
        $year,
        $price,
        $numberOfDoors
    ) {
        parent::__construct(
            $vehicleId,
            $brand,
            $model,
            $year,
            $price
        );

        $this->numberOfDoors = $numberOfDoors;
    }

    public function displayDetails()
    {
        parent::displayDetails();

        echo "Vehicle Type: Car\n";
        echo "Number of Doors: " . $this->numberOfDoors . "\n";
    }
}