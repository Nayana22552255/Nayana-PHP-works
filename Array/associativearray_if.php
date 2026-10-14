<?php

$cars=[
    "bmw" => "red",
    "porsche" => "yellow"
];
foreach ( $cars as $car => $color ){
    echo "the color of $car is $color.\n";

}
echo "\n";

$persons=[
    "nayana" => 22,
    "anaswara" => 22,
    " gopika" => 24
];
foreach ($persons as $person => $age){
    echo "name is $person and age is $age . \n";
    }

echo"\n";


$person=[
    "nayana" => 22,
    "gopika" => 24,
    "anaswara" => 22
];
  
$keys = array_keys($person);

for ($i =0; $i < count($keys); $i++){       //Associative array using for loop
    $name= $keys[$i];
    echo "name is $name is $person[$name].\n";
}
echo"\n";



$data=[
    "x" => 100,
    "y" => 200
];

if ( array_key_exists("x",$data)){          //check if a key is exists
    echo "key 'x' is present in the array.\n ";
}
if (isset($data["z"])){
    echo "key 'z' is set.\n";
}
else{
    echo "key 'z'is not exist.\n";
}
echo "\n";




?>