<?php
class BankAccount
{
    private $accountnumber;
    private $accountholder;
    private $balance;

    public function __construct($accountnumber,$accountholder,$balance)
    {
        $this->accountnumber=$accountnumber;
        $this->accountholder=$accountholder;
        $this->balance=$balance;
    
    }

    public function getAccountnumber()
    {
         return $this->accountnumber;
    }
    public function getAccountholder()
    {
        return $this->accountholder;
    }
    public function getBalance()
    {
        return $this->balance;
    }

    public function displayaccount()
    {
        echo "Account number: ".$this->accountnumber."\n";
        echo "Account holder: ".$this->accountholder."\n";
        echo "Balance: ".$this->balance."\n";


    }

    public function Deposit($amount)
    {
            if( $amount > 0){
                $this->balance += $amount;
                echo "$amount deposited successfully\n";
            }
            
    }

    public function Withdraw($amount)
    {
        if($amount>0 && $amount <= $this->balance)
            {
                $this->balance = $this->balance - $amount;
                echo "$amount withdraw successfully\n";
            }
            else{
                echo "Insufficient balance\n";
            }
    }
}
?>