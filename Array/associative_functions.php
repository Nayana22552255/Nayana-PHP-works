<?php

$item=[
    "f" => "one",
    "s" => "two",
    "t" => "three"
];
unset($item["second"]);     // removing an element from the associative array
print_r($item); //also use var_dump($item)
//$keys=array_keys($item);
//print_r($keys);
print_r(array_keys($item));         //return all keys of the array
print_r(array_values($item));       //return all the values of the array
print_r(array_key_exists("f",$item));   //checks the key exists
echo"\n";
print_r(count($item));      // return the number of elements
echo "\n";
unset($item["s"]);      //used to remove a key value pair
print_r($item);

?>