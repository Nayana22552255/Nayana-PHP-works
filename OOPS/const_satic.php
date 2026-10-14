<?php
class math{
    const PI=3.14;  //constant property
    public static $counter=0;  //self keyword is used to refer to the current class
    }
echo  math:: PI;  //accessing the constant property of the class math
echo "\n";
echo math::$counter;
?>
