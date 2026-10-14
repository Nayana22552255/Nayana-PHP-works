<?php
class Fooditem
{
    private $name;
    private $price;
    

public function __construct($name,$price)
{
    $this->name=$name;
    $this->price=$price;
}

public function getname()
{
    return $this->name;
}

public function getprice()
{
    return $this->price;
}

public function Displayfood()
{
    echo ": ".$this->name. " ". "Price: ".$this->price. "\n";
}
}
?>