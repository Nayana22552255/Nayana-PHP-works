<?php

class Player
{
    private $name;
    private $symbol;

    
    public function __construct($name, $symbol)             //give parameters to construct
    {
        $this->name = $name;
        $this->symbol = $symbol;
    }

    public function getName()                       //getting the name of the player 
    {
        return $this->name;
    }

   
    public function getSymbol()                       //getting the symbol of the player
    {
        return $this->symbol;
    }
}

?>