<?php

class VehicleManager
{
    private $vehicles = [];

    public function addVehicle(Vehicle $vehicle)
    {
        $this->vehicles[] = $vehicle;

        echo "Vehicle added successfully.\n\n";
    }

    public function displayAllVehicles()
    {
        if (empty($this->vehicles)) {
            echo "No vehicles available.\n";
            return;
        }

        foreach ($this->vehicles as $vehicle) {
            $vehicle->displayDetails();

            echo "-------------------------\n";
        }
    }

    public function rentVehicle($vehicleId)
    {
        foreach ($this->vehicles as $vehicle) {

            if ($vehicle->getVehicleId() == $vehicleId) {
                $vehicle->rentVehicle();
                return;
            }
        }

        echo "Vehicle not found.\n";
    }

    public function returnVehicle($vehicleId)
    {
        foreach ($this->vehicles as $vehicle) {

            if ($vehicle->getVehicleId() == $vehicleId) {
                $vehicle->returnVehicle();
                return;
            }
        }

        echo "Vehicle not found.\n";
    }
}