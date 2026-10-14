<?php
class Agecalculator
{

    public function calculateage($dateofbirth)
    {
            $currentdate=date("y-m-d");
            $birthdate=new DateTime("$dateofbirth");
            $today=new DateTime($currentdate);


        $age = $birthdate->diff($today);

        echo "\n------------------\n";
        echo "age\n";
        echo $age->y."year \n";
        echo $age->m."month \n";
        echo $age->d."date \n";
                
    }
}
?>