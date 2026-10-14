<?php

require_once("Car.php");
require_once("Bike.php");
require_once("Truck.php");
require_once("VehicleManager.php");

$manager = new VehicleManager();

$car = new Car(
    101,
    "Toyota",
    "Innova",
    2023,
    2500000,
    4
);

$bike = new Bike(
    102,
    "Yamaha",
    "R15",
    2024,
    200000,
    155
);

$truck = new Truck(
    103,
    "Tata",
    "Prima",
    2022,
    3500000,
    10
);

$manager->addVehicle($car);
$manager->addVehicle($bike);
$manager->addVehicle($truck);

echo "Vehicle Management System<\n";

echo "All Vehicles\n";

$manager->displayAllVehicles();

echo "Rent Vehicle\n";

$manager->rentVehicle(102);

echo "Vehicle Details After Renting\n";

$manager->displayAllVehicles();

echo "Return Vehicle\n";

$manager->returnVehicle(102);

echo "Vehicle Details After Returning\n";

$manager->displayAllVehicles();