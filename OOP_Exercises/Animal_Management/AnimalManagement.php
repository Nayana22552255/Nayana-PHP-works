
<?php

// STEP 1Parent class
class Animal
{
    private  $id;
    private  $name;
    private  $species;
    private  $age;
    private  $health;

    public function __construct(
         $id,
         $name,
         $species,
         $age,
         $health
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->species = $species;
        $this->age = $age;
        $this->health = $health;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSpecies()
    {
        return $this->species;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getHealth()
    {
        return $this->health;
    }

    public function setHealth( $health)
    {
        $this->health = $health;
    }

    public function feed()
    {
        echo $this->name . " has been fed.\n";
    }

    public function makeSound()
    {
        echo $this->name . " makes a sound.\n";
    }

    public function displayDetails()
    {
        echo "ID" . $this->id . "\n";
        echo "Name" . $this->name . "\n";
        echo "Species" . $this->species . "\n";
        echo "Age" . $this->age . " years\n";
        echo "Health" . $this->health . "\n";
    }

    // Convert the object to an array for JSON storage
    public function toArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "species" => $this->species,
            "age" => $this->age,
            "health" => $this->health,
            "type" => get_class($this)
        ];
    }
}



class Dog extends Animal
{
    public function makeSound()
    {
        echo $this->getName() . " says Woof!\n";
    }
}

class Bird extends Animal
{
    public function makeSound()
    {
        echo $this->getName() . " says Chirp!\n";
    }
}


class AnimalManagement
{
    private $animals = [];
    private  $file;

    public function __construct( $file)
    {
        $this->file = $file;

        // Create the JSON file if it does not exist
        if (!file_exists($this->file)) {
            file_put_contents($this->file, "[]");
        }

        $this->loadAnimals();
    }

    // Load saved records from JSON
    private function loadAnimals()
    {
        $json = file_get_contents($this->file);
        $records = json_decode($json, true);

        if (!is_array($records)) {
            echo "Invalid JSON data. Starting with an empty list.\n";
            return;
        }

        foreach ($records as $record) {
            if (
                !is_array($record) ||
                !isset(
                    $record["id"],
                    $record["name"],
                    $record["species"],
                    $record["age"],
                    $record["health"]
                )
            ) {
                continue;
            }

            $id = $record["id"];

            if (isset($this->animals[$id])) {
                continue;
            }

            $name = $record["name"];
            $species =  $record["species"];
            $age =  $record["age"];
            $health =  $record["health"];
            $type = $record["type"] ?? "Animal";

            // Recreate the correct object type
            if ($type === "Dog") {
                $animal = new Dog(
                    $id, $name, $species, $age, $health
                );
            } elseif ($type === "Bird") {
                $animal = new Bird(
                    $id, $name, $species, $age, $health
                );
            } else {
                $animal = new Animal(
                    $id, $name, $species, $age, $health
                );
            }

            $this->animals[$id] = $animal;
        }
    }

    // Save all current records o JSON
    private function saveAnimals()
    {
        $records = [];

        foreach ($this->animals as $animal) {
            $records[] = $animal->toArray();
        }

        $json = json_encode($records, JSON_PRETTY_PRINT);

        if (
            $json === false ||
            file_put_contents($this->file, $json) === false
        ) {
            echo "Error Unable to save animal data.\n";
        }
    }

    // Add an animal
    public function addAnimal(Animal $animal)
    {
        $id = $animal->getId();

        if (isset($this->animals[$id])) {
            echo "Animal ID already exists.\n";
            return;
        }

        $this->animals[$id] = $animal;
        $this->saveAnimals();

        echo "Animal added and saved successfully.\n";
    }

    // Display all animals
    public function displayAllAnimals()
    {
        if (empty($this->animals)) {
            echo "No animals registered.\n";
            return;
        }

        foreach ($this->animals as $animal) {
            echo "\n--------------------\n";
            $animal->displayDetails();
        }
    }

    // Search by ID
    public function searchAnimal( $id)
    {
        return $this->animals[$id] ?? null;
    }

    // Feed an animal
    public function feedAnimal( $id)
    {
        $animal = $this->searchAnimal($id);

        if ($animal !== null) {
            $animal->feed();
        } else {
            echo "Animal not found.\n";
        }
    }

    // Update health
    public function updateHealth(
         $id,
         $health
    ) {
        $animal = $this->searchAnimal($id);

        if ($animal === null) {
            echo "Animal not found.\n";
            return;
        }

        $animal->setHealth($health);
        $this->saveAnimals();

        echo "Health updated and saved successfully.\n";
    }

    // Display an animal's sound
    public function showSound( $id)
    {
        $animal = $this->searchAnimal($id);

        if ($animal !== null) {
            $animal->makeSound();
        } else {
            echo "Animal not found.\n";
        }
    }

    // Remove an animal
    public function removeAnimal( $id)
    {
        if (!isset($this->animals[$id])) {
            echo "Animal not found.\n";
            return;
        }

        unset($this->animals[$id]);
        $this->saveAnimals();

        echo "Animal removed and changes saved.\n";
    }
}



$manager = new AnimalManagement(__DIR__ . "/Animal.json");



while (true) {
    echo "\n===== ANIMAL MANAGEMENT SYSTEM =====\n";
    echo "1. Add animal\n";
    echo "2. Display all animals\n";
    echo "3. Search animal\n";
    echo "4. Feed animal\n";
    echo "5. Update health\n";
    echo "6. Remove animal\n";
    echo "7. Make animal sound\n";
    echo "8. Exit\n";

    $choice = trim(readline("Enter your choice"));

    switch ($choice) {
        case "1":
            $id = filter_var(
                readline("Enter animal ID")
               
            );

            if ($id === false || $id <= 0) {
                echo "Enter a valid positive ID.\n";
                break;
            }

            $name = trim(readline("Enter animal name"));
            $species = trim(readline("Enter species"));

            $age = filter_var(
                readline("Enter age")
            );

            if (
                $name === "" ||
                $species === "" ||
                $age === false ||
                $age < 0
            ) {
                echo "Enter valid animal details.\n";
                break;
            }

            $health = trim(readline("Enter health condition"));

            if ($health === "") {
                echo "Health condition cannot be empty.\n";
                break;
            }

            echo "Choose type1. General animal  2. Dog  3. Bird\n";
            $type = trim(readline("Enter type"));

            if ($type === "2") {
                $animal = new Dog(
                    $id, $name, $species, $age, $health
                );
            } elseif ($type === "3") {
                $animal = new Bird(
                    $id, $name, $species, $age, $health
                );
            } elseif ($type === "1") {
                $animal = new Animal(
                    $id, $name, $species, $age, $health
                );
            } else {
                echo "Invalid animal type.\n";
                break;
            }

            $manager->addAnimal($animal);
            break;

        case "2":
            $manager->displayAllAnimals();
            break;

        case "3":
            $id = filter_var(
                readline("Enter ID to search")
                
            );

            if ($id === false) {
                echo "Invalid ID.\n";
                break;
            }

            $animal = $manager->searchAnimal($id);

            if ($animal !== null) {
                $animal->displayDetails();
            } else {
                echo "Animal not found.\n";
            }
            break;

        case "4":
            $id = filter_var(
                readline("Enter ID to feed")
              
            );

            if ($id === false) {
                echo "Invalid ID.\n";
                break;
            }

            $manager->feedAnimal($id);
            break;

        case "5":
            $id = filter_var(
                readline("Enter ID")
                
            );

            if ($id === false) {
                echo "Invalid ID.\n";
                break;
            }

            $health = trim(
                readline("Enter new health condition")
            );

            if ($health === "") {
                echo "Health condition cannot be empty.\n";
                break;
            }

            $manager->updateHealth($id, $health);
            break;

        case "6":
            $id = filter_var(
                readline("Enter ID to remove")
               
            );

            if ($id === false) {
                echo "Invalid ID.\n";
                break;
            }

            $manager->removeAnimal($id);
            break;

        case "7":
            $id = filter_var(
                readline("Enter ID")
               
            );

            if ($id === false) {
                echo "Invalid ID.\n";
                break;
            }

            $manager->showSound($id);
            break;

        case "8":
            echo "Animal Management System closed.\n";
            exit;

        default:
            echo "Invalid choice. Try again.\n";
    }
}
