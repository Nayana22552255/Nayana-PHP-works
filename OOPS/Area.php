<?php
abstract class Shape                                
{
    abstract public function area();
}

class Circle extends Shape
{
        public $radius;

        public function __construct($radius)
        {
             $this->radius=$radius;
        }

        public function area()
        {
             return 3.14 * $this->radius * $this->radius;
        }
}

class Rectangle  extends Shape
{
        public $length;
        public $width;

        public function __construct($length, $width)
        {
            $this->length = $length;
            $this->width = $width;
        }
        public function area()
        {
            return $this->length * $this->width;        }
}


$circle = new Circle(5);
echo "area of circle". $circle->area()."\n";

$rectangle= new Rectangle(5,5);
echo "area of rectangle". $rectangle->area(),"\n"


?>