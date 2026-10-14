<?php
abstract class Animal{                  //create a abstract class
        abstract public function sound();       //create an abstract method

}
class Dog extends Animal{

    public function sound(){
        echo "Dog barks";
    }

}

$dog = new Dog();
$dog->sound();
?>