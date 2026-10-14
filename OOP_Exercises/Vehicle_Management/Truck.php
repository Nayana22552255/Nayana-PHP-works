<?php

require_once("Vehicle.php");

class Truck extends Vehicle
{
    private $loadCapacity;

    public function __construct(
        $vehicleId,
        $brand,
        $model,
        $year,
        $price,
        $loadCapacity
    ) {
        parent::__construct(
            $vehicleId,
            $brand,
            $model,
            $year,
            $price
        );

        $this->loadCapacity = $loadCapacity;
    }

    public function displayDetails()
    {
        parent::displayDetails();

        echo "Vehicle Type: Truck\n";
        echo "Load Capacity: " . $this->loadCapacity . " Tons\n";
    }
}