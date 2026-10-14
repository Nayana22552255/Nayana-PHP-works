<?php
class Calculator
{
    public function __call($method,$arguments)

    {
        if($method == "add")
            {
                $count= count($arguments);

                if($count == 2)
                    {
                        return $arguments[0] + $arguments[1];
                    }
                elseif($count == 3)
                    {
                        return $arguments[0] + $arguments[1] + $arguments[2];
                    }
                else
                    {
                      echo "Invalid number of arguments\n";
                    }
            }
    }
}

$calc= new Calculator();

echo $calc->add(10,20);
echo "\n";
echo $calc->add(10,20,30);
echo "\n";

?>