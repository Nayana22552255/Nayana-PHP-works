<?php
class Restaurant
{
     private $menu=[];

     public function addFood(Fooditem  $food)
    {
            $this->menu[]=$food;

            echo "\n Food item added successfully\n";
    }

    public function displaymenu()
    {
        foreach($this->menu as $index=>$food)
            {
                echo ($index + 1);
                $food->Displayfood();
            }
    }

    public function calculatebill($foodnumber, $quantity)
    {
            $index=$foodnumber - 1;
            
            if(isset($this->menu[$index]))
                {
                    $food=$this->menu[$index];
                    return $food->getprice($food) * $quantity;
                }
                return 0;
    }


}
?>