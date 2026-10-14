<?php
function factorial($n){
    if($n==0 || $n==1){
        return 1;
    }
    elseif($n<0){
        echo"invalid input";
    }
    else{
        return $n * factorial($n-1);
    }
}
$n=readline("enter a number you want to find factorial");
echo "factorial of $n is:".factorial($n);

?>