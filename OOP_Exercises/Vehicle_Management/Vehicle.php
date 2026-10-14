<?php

class Vehicle
{
    private $vehicleId;
    private $brand;
    private $model;
    private $year;
    private $price;
    private $available;

    public function __construct($vehicleId, $brand, $model, $year, $price)
    {
        $this->vehicleId = $vehicleId;
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->price = $price;
        $this->available = true;
    }

    public function getVehicleId()
    {
        return $this->vehicleId;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function isAvailable()
    {
        return $this->available;
    }

    public function rentVehicle()
    {
        if ($this->available) {
            $this->available = false;
            echo "Vehicle rented successfully\n";
        } else {
            echo "Vehicle is already rented\n";
        }
    }

    public function returnVehicle()
    {
        $this->available = true;
        echo "Vehicle returned successfully\n";
    }

    public function displayDetails()
    {
        echo "Vehicle ID: " . $this->vehicleId . "\n";
        echo "Brand: " . $this->brand . "\n";
        echo "Model: " . $this->model . "\n";
        echo "Year: " . $this->year . "\n";
        echo "Price: ₹" . $this->price . "\n";
        echo "Availability: " .
            ($this->available ? "Available" : "Not Available") . "\n";
    }
}