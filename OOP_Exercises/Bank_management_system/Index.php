<?php
require_once("Bankaccount.php");
require_once("SavingsAccount.php");
require_once("CurrentAccount.php");

$bank=new BankAccount(12121,"Nayana",2000);

//display account details
echo "Acoount details";
echo $bank->displayaccount();
echo "\n";

//deposit
echo "Deposit\n";
$bank->Deposit(1000);
echo "current balance".$bank->getBalance()."\n";
echo"\n";

echo "Withdraw\n";
$bank->Withdraw(500);
echo "current balance".$bank ->getBalance()."\n";

//savings Account(Interest rate)
echo "\n";
echo "display account\n";
$savings=new SavingsAccount(1098,"Anu",50000,5);
echo $savings->displayaccount();
echo "interest: ".$savings ->calacualteintrest()."\n";

//Current Account(overdraft limit)
echo"\n";
echo "Display Account\n";
$current=new CurrentAccount(1234,"gopika",10000,5000);
echo $current->displayaccount();
echo $current->withdraw(13000);
?>