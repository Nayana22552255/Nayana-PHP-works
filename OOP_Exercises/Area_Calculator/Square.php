<?php
require_once("Shape.php");

 class Square extends Shape
 {
    private $side;
    public function __construct($side)
    {
            $this->side=$side;
    }

    public function getCalculateArea()
    {
        return $this->side * $this->side;
    }
 } 
 ?>