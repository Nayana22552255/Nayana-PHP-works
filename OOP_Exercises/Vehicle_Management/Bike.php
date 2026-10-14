<?php

require_once("Vehicle.php");

class Bike extends Vehicle
{
    private $engineCapacity;

    public function __construct(
        $vehicleId,
        $brand,
        $model,
        $year,
        $price,
        $engineCapacity
    ) {
        parent::__construct(
            $vehicleId,
            $brand,
            $model,
            $year,
            $price
        );

        $this->engineCapacity = $engineCapacity;
    }

    public function displayDetails()
    {
        parent::displayDetails();

        echo "Vehicle Type: Bike\n";
        echo "Engine Capacity: " . $this->engineCapacity . " CC\n";
    }
}