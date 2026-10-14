<?php

class Flames
{
    private $name1;
    private $name2;

    private  $flames=[
            "F"=>"Friends",
            "L"=>"Love",
            "A"=>"Affection",
            "M"=>"Marriage",
            "E"=>"Enemy",
            "S"=>"Siblings"
    ];


    public function __construct($name1, $name2)
    {
        $this->name1=strtolower(preg_replace('/[^a-z]/i', ' ', $name1));
        $this->name2=strtolower(preg_replace('/[^a-z]/i', ' ', $name2));
    }


    public function  countRemainingLetters()
    {
            $letters1=str_split($this->name1);
            $letters2=str_split($this->name2);


            foreach($letters1 as $index => $letter)
                {
                    $matchindex=array_search($letter, $letters2,true);


                    if($matchindex !== false)
                        {
                            unset($letters1[$index]);
                            unset($letters2[$matchindex]);
                        }
                    
                }


                $letters1=array_values($letters1);
                $letters1=array_values($letters2);


                return  count($letters1) + count($letters2);
    }


    public function findrelationship()
    {
            if($this->name1 === ' ' || $this->name2 === '')
                {
                    echo "please enter two valid names\n";
                    return;

                }

                $count = $this->countRemainingLetters();


                if($count === 0)
                    {
                        echo "No remaining letters. The falmes result cannot be determined\n";
                        return ;
                    }


                $flames=array_keys($this->flames);
                $index=0;


                while(count($flames) > 1)
                    {
                        $index=($index + $count - 1) % count($flames);

                        array_splice($flames,$index,1);
                    }

                    $result=$flames[0];

                    echo "\n Name 1: ". strtoupper($this->name1). "\n";
                    echo "Name 2: ".strtoupper($this->name2). "\n";
                    echo "Rmaining letter count: ".$count. "\n";
                    echo "Flames result: ".$this->flames[$result]. "\n";

    }

}
 echo "=======Flames Gmae======\n";

 $name1=readline("enter the first name: ");
 $name2=readline("enter the second name: ");


 $game=new Flames($name1,$name2);
 $game->findrelationship();
?>