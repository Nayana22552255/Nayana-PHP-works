<?php
$color=["pink","maroon","white"];
array_push($color,"black");     //adding new items in array at the end of the array
var_dump($color);
echo "\n";
array_push($color,"green");
var_dump($color);
echo "\n";
array_pop($color);      //remove item from the end of the array
var_dump($color);
array_shift($color);    //remove the first element from  the array 
var_dump($color);
array_unshift($color,"red");     //add elements at the beginning of the array
var_dump($color);
echo "\n";

?>