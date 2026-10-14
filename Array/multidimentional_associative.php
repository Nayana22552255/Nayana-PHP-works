<?php

$marks=array(               //Multidimensional associative array
        "nayana"=>array(
            "c"=>80,
            "python"=>70
        ),
        "anaswara"=>array(
            "c" =>70,
            "python"=>80
        )
);
print_r($marks);
echo "\n";
echo $marks["nayana"]["c"];
?>