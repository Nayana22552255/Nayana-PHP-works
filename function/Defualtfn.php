<?php
//Default function
function  name($fname="nayana", $lname="santhosh"){             //default function
        echo "firstname is".$fname."\n";
        echo "last name is".$lname."\n";

}
name();
echo "\n";




//Anonymous function
$greetings=function(){      //using anonymous function
    $a=5;
    $b=10;
    echo $a * $b;
   
};
$greetings();
echo "\n";


$number=[1,2,3,4,5];                    //declairing an array called number
$squared=array_map(function($n){        //declairing the anonymous function and map the anonymous function to the array
    return $n ** 2;
}, $number);
print_r($squared);
echo "\n";




//callback function
function studentmark($name,$marks,$callback){           //craeted the main function and pass three arguments
    echo "name is ".$name." "." mark is ".$marks." "."\n";
    $result=$callback($marks);          //executing the callback 
    echo "result:".$result;

}
function checkresult($marks){           //created the callbackfuction and pass the mark parameter in the main function
    if($marks >= 55){
        return "pass";
    }
    else{
        return "fail";
    }
}
studentmark("nayana",75,"checkresult");     //pass function as argument(checkresult)
echo "\n";



function applycallback($number,$callback){
    foreach($number as $number){
        echo $callback($number);
        echo "\n";
    }
}
function  sqaure($number)
{
    return $number * $number;
}
$number=[5,6,7,8,9,10];
applycallback($number,"sqaure");
echo "\n";


//arrow function eg:1
$namelength= fn($name) => strlen($name);
$name = "nayana";
echo "student name: ".$name."\n";
echo "name length: ".$namelength($name);
echo "\n";


//arrow function eg:2
$array=[1,2,3,4,5];
print_r($array);
$square=fn($number) => $number * $number;
foreach ($array as $number){
echo "squared array number " .$square($number). "\n";
}
echo "\n";


//datetime function
    echo date("Y-m-d");         //date function
    echo "\n";
   echo date("Y-m-d H:i:s");        //date and time where H= hours,i gives= minutes, s gives=second 
?>