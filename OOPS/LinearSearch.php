<?php

class LinearSearch
{
    private $numbers=[];

    public function generateArray()         //generating an array with random numbers
    {
        for($i =0; $i <10 ; $i++)
            {
                    $this->numbers[]=rand(1,50);
            }
    }

    public function DisplayArray()
    {
        echo " Random Numbers \n";
        print_r($this->numbers);

    }

    public function Search($value)
    {
        for($i= 0; $i<count($this->numbers); $i++)
            {
                if($this->numbers[$i] == $value)
                    {
                        return $i;
                    }

                   
            }
             return -1;
    }
}

$linearsearch= new LinearSearch();

$linearsearch->generateArray();
$linearsearch->DisplayArray();

$value=readline("enter the value you want to search");
$position=$linearsearch->Search($value);

if($position != -1)
{
    echo "value $value found at the index $position \n";

}
else{
    echo "$value not fount\n";

}

?>