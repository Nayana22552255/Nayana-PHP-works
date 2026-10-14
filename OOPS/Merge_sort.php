<?php

class Mergesort
{
    //private $array;
    public function Sort($array)
    {
        if(count($array) <= 1)
            {
                return $array;
            } 

        $middle=floor(count($array)/2);

        $left=array_slice($array, 0, $middle);
        $right=array_slice($array,$middle);

        $left=$this->sort($left);           //recusively sorting
        $right=$this->sort($right);


        return $this->merge($left,$right);

    }

    public function Merge($left,$right)
    {
        $result=[];
        $i=0;
        $j=0;

        while($i < count($left) && $j < count($right))
            {
                if($left[$i] < $right[$j])
                    {
                        $result[]=$left[$i];
                        $i++;
                    }
                    else
                        {
                            $result[]=$right[$j];
                            $j++;
                        }
            }

        while($i < count($left))
            {
                $result[]=$left[$i];
                $i++;
            }

        while($j < count($right))
            {
                $result[]=$right[$j];
                $j++;
            }

            return $result;
    }

}

$sort=new Mergesort();

$array=[34,15,78,54,37,25];

echo "original array: \n";
print_r($array);

$sorttedarray=$sort->Sort($array);

echo "Sorted array: \n";
print_r($sorttedarray);

?>