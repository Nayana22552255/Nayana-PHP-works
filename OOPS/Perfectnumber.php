<?php 

class PerfectNumber
{
    public function check($number)
    {
        $sum = 0;
        for ($i = 1; $i < $number; $i++)
            {
                if ($number % $i == 0){
                $sum = $sum + $i;
              }
            }
        if ($sum == $number) {
            return true;
        }
    return false;
    }
}

$perfectNumber = new PerfectNumber();
$number = readline("Enter a number: ");

if ($perfectNumber->check($number)){
    echo $number . " is a perfect number.\n";
}
else {
    echo $number . " is not a perfect number.\n";
}