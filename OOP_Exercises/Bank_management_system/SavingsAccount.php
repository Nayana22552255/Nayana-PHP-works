<?php
require_once("BankAccount.php");

class SavingsAccount extends BankAccount
{
    private $interestrate;
    public function __construct($accountnumber,$accountholder,$balance,$interestrate)
    {
                parent:: __construct($accountnumber,$accountholder,$balance);
                
                    $this -> interestrate=$interestrate;
                
    }
    public function calacualteintrest()
    {
        return $this ->getBalance() * $this->interestrate / 100 ;
    }
}

?>