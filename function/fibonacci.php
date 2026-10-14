<?php

function fib($n)
{
    if($n ==0 )
        {
            return 0;
        }
    if ($n == 1)
        {
            return 1;
        }
    return fib($n-1) + fib($n-2);
}
$number=10;
echo "fibonacci series is\n";
for($i =0; $i< $number; $i++)
    {
        
        echo fib($i);
    
    }
?>