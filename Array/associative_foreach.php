<?php
$fruits = [
    "apple" => "red",
    "banana" => "yellow",
    "grape" => "purple"
];

foreach ($fruits as $fruit => $color) {         //associative array using foreach
    echo "The color of $fruit is $color.\n";
}
echo"\n";
?>