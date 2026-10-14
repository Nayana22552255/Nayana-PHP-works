<?php
class  employee{
    public $name="nayana";
    public function __construct($name){
        $this->name=$name;
    }
     
    public function display(){
        echo "my name is $this->name \n";
    }
}
class company extends employee{
    public $position;
    public function __construct($position){
            $this->position=$position;
    }
    public function get(){
        echo"my position is $this->position \n";
    }

}

$obj= new company("manager");
$obj->display();
$obj->get();
?>
