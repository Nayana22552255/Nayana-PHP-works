<?php
require_once("Shape.php");
class Triangle extends Shape
{
    private $base;
    private $height;

    public function __construct($base, $height)
    {
         $this->base=$base;
         $this->height=$height;
    }

    public function getCalculateArea()
    {
            return 0.5 * $this->base * $this->height;
    }
    
}
?>