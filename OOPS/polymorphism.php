<?php
class UPI
{
    public function pay()
    {
        echo "payment made using UPI \n";
    }
}
class Creaditcard
{
    public function pay()
    {
        echo "payment made using Creaditcard \n";
    }
}
class Cash{
    public function  pay()
    {
        echo "payment is made using cash \n";
    }
}

$upi = new UPI();
$creaditcard= new Creaditcard();
$cash = new Cash();

echo $upi->pay();
echo $creaditcard->pay();
echo $cash->pay();
?>