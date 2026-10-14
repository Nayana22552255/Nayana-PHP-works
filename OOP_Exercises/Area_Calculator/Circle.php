<?php
require_once("Shape.php");
Class Circle extends Shape
{
    private $radius;
    public function __construct($radius)
    {
        $this->radius=$radius;
    }
    public function getCalculateArea()
    {
            return pi() * $this->radius * $this->radius; 
    }
    
}
?>