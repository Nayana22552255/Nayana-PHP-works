<?php

class OddOneOut
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    
    public function addCategory()                                       // Add a new category and list of items
    {
        echo "\n===== ADD ODD ONE OUT DATA =====\n";

        $category = readline("Enter category: ");

        $count = (int) readline("How many items do you want to enter? ");

        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $item = readline("Enter item " . ($i + 1) . ": ");
            $items[] = $item;
        }

        $odd = readline("Enter the odd one out: ");

        $data = $this->loadData();

        $newData = [
            "category" => $category,
            "items" => $items,
            "odd" => $odd
        ];

        $data[] = $newData;

        $this->saveData($data);

        echo "\nData saved successfully!\n";
    }

    // Load data from JSON file
    private function loadData()
    {
        if (!file_exists($this->filename)) {
            return [];
        }

        $json = file_get_contents($this->filename);

        if (empty($json)) {
            return [];
        }

        return json_decode($json, true);
    }

   
    private function saveData($data)                             // Save data into JSON file
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents($this->filename, $json);
    }

    // Display all categories
    public function displayCategories()
    {
        $data = $this->loadData();

        if (empty($data)) {
            echo "\nNo categories found.\n";
            return;
        }

        echo "\n===== AVAILABLE CATEGORIES =====\n";

        foreach ($data as $index => $category) {
            echo ($index + 1) . ". " . $category["category"] . "\n";
        }
    }

    // Play the game
    public function play()
    {
        $data = $this->loadData();

        if (empty($data)) {
            echo "\nNo game data available.\n";
            return;
        }

        echo "\n===== SELECT CATEGORY =====\n";

        foreach ($data as $index => $category) {
            echo ($index + 1) . ". " . $category["category"] . "\n";
        }

        $choice = (int) readline("Choose a category: ");

        if ($choice < 1 || $choice > count($data)) {
            echo "\nInvalid category!\n";
            return;
        }

        $selected = $data[$choice - 1];

        echo "\n===== ODD ONE OUT GAME =====\n";
        echo "Category: " . $selected["category"] . "\n\n";

        $items = $selected["items"];

        foreach ($items as $index => $item) {
            echo ($index + 1) . ". " . $item . "\n";
        }

        $answer = (int) readline("\nWhich item is the odd one out? ");

        if ($answer < 1 || $answer > count($items)) {
            echo "\nInvalid choice!\n";
            return;
        }

        $selectedItem = $items[$answer - 1];

        echo "\nYou selected: " . $selectedItem . "\n";

        if (strtolower($selectedItem) == strtolower($selected["odd"])) {
            echo "Correct!\n";
        } else {
            echo "Wrong answer!\n";
            echo "The correct answer is: " . $selected["odd"] . "\n";
        }
    }
}


// Create object
$game = new OddOneOut("odd_one_out.json");


// Main menu
while (true) {

    echo "\n============================\n";
    echo "       ODD ONE OUT GAME\n";
    echo "============================\n";

    echo "1. Add Category\n";
    echo "2. View Categories\n";
    echo "3. Play Game\n";
    echo "4. Exit\n";

    $choice = readline("Enter your choice: ");

    switch ($choice) {

        case "1":
            $game->addCategory();
            break;

        case "2":
            $game->displayCategories();
            break;

        case "3":
            $game->play();
            break;

        case "4":
            echo "\nThank you for playing!\n";
            exit;

        default:
            echo "\nInvalid choice!\n";
    }
}
?>