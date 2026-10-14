<?php
class A{
    public $name;
    public function get()
    {
        echo "name is $this->name \n";
     
    }
}
class B extends A{
    public $age;
    public function getage(){
        echo "age is $this->age \n";

    }
}
class C extends B{
    public $place;
    public function display(){
        echo " place is $this->place \n";
    }
}
$obj= new C();
$obj->name="nayana";
$obj->age=22;
$obj->place="kannur";
$obj->get();
$obj->getage();
$obj->display();
?>