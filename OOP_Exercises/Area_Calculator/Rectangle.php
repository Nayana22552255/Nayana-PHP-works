<?php
 require_once("Shape.php");

 class Rectangle extends Shape
 {
    private $length;
    private $width;

    public function __construct($length,$width)
    {
        $this->length=$length;
        $this->width=$width;
    }

    public function getCalculateArea()
    {
        return $this->length * $this->width;
    }
 }
 ?>