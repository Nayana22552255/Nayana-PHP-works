<?php
require_once("Rectangle.php");
require_once("Square.php");
require_once("Triangle.php");
require_once("Circle.php");

while(true){
    echo "\n===== AREA CALCULATOR =====\n";
    echo "1. Circle\n";
    echo "2. Rectangle\n";
    echo "3. Square\n";
    echo "4. Triangle\n";
    echo "5. Exit\n";

    $choice=readline("enter your choice: ");

    if($choice == 1){
        
        $radius=readline("enter the radius: ");
        $circle=new Circle($radius);
        echo "Area of Circle: ".$circle->getCalculateArea()."\n";
        echo "\n";
    }elseif($choice == 2){

        $length=readline("enter the length: ");
        $width=readline("enter the width: ");
        $rectangle=new Rectangle($length,$width);
        echo "Area of rectangle: ".$rectangle->getCalculateArea()."\n";
        echo "\n";

    }elseif($choice == 3){
        
        $side=readline("enter the side: ");
        $square= new Square($side);
        echo "Area of Square: ".$square->getCalculateArea()."\n";
        echo "\n";
    }elseif($choice == 4)
    {
        $base=readline("enter the base: ");
        $height=readline("enter the height: ");
        $triangle=new Triangle($base,$height);
        echo "Area of Triangle: ".$triangle->getCalculateArea()."\n";
        echo "\n";
    }elseif($choice == 5)
    {
        echo "Exit\n";
        break;
    } 
    else{
        echo "invalid input\n";
    }   
}
?>
