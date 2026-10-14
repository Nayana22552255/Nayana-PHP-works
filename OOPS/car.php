<?php
class Car{
    public $model,$color,$price;  //properties
    public function __construct($model,$color,$price){  //constructor which is used to initialize the properties of the class
        $this->model=$model; //this keyword is used to refer to the current object of the class
        $this->color=$color;
        $this->price=$price;
    }
    public function display(){  //method to display the properties of the class
        echo "  the model of the car: " . $this->model . "\n";
        echo "  the color of the car: " . $this->color . "\n";
        echo "  the price of the car: " . $this->price . "\n";
    }
    public function __destruct(){   //destructor which is used to destroy the object of the class
        echo $this->model ." object is destroyed\n";
    }   
}
$car1=new Car("BMW","green",1000000);   //creating an object of the class Car and passing the values to the constructor
$car2=new Car("Audi","red",2000000);
$car1->display();   //calling the display method of the class Car to display the properties of the object
$car2->display();
?>