<?php
require_once("BankAccount.php");
class CurrentAccount extends BankAccount
{
    private $overdraftlimit;
    public function __construct($accountnumber,$accountholder,$balance,$overdraftlimit)
    {
        parent ::__construct($accountnumber,$accountholder,$balance);
                $this ->overdraftlimit = $overdraftlimit;
    }

     public function withdraw($amount)
     {
            if($amount <= $this->getBalance() + $this->overdraftlimit)
                {            
                    echo " $amount withdraw from current account \n";
            }else{
                        echo "failed";
            }
     }


}
?>