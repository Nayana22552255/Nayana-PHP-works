<?php
require_once("Fooditem.php") ;
require_once("Restaurant.php");

$restaurant= new Restaurant();
while(true)
    {
            echo "\n=======Restaurant========\n ";
            echo "1.Add Food\n";
            echo "2.Display Menu\n";
            echo "3.Total Amount\n";

            $choice=readline("enter your choice");

            switch($choice)
            {
                case 1:
                    $name=readline("enter the item name: ");
                    $price=readline("enter the item price: ");
                    $food= new Fooditem($name,$price);
                    $restaurant->addFood($food);
                    break;

                case 2: 
                    echo "Item Name: ".$restaurant->name. "\n";
                    echo "Item Price: ".$restaurant->price. "\n";
                    $restaurant->displaymenu();
                    break;

                case 3:
                    $restaurant->displaymenu();
                    $foodnumber=readline("enter the food number");
                    $quantity= readline("enter the Quantity");
                   
                     $bill= $restaurant->calculatebill($foodnumber,$quantity);
                      echo "Total Amount: ".$bill. "\n";

                case 4:
                    echo "Thank you! welcome back!\n";
                    exit;
                
                case 4: 
                    echo "invalid choice\n";

            }

    }

?>