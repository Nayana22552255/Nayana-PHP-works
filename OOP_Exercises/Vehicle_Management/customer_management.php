<?php
class Customermanager
{
    private $Customerid;
    private $Customername;
    private $email;
    private $phone;

    public function __construct($Customerid,$Customername,$email,$phone)
    {
        $this->Customerid=$Customerid;
        $this->Customername=$Customername;
        $this->email=$email;
        $this->phone=$phone;
    }

    public function getcustomerid()
    {
                return $this->Customerid;
    }
    public function getcustomername()
    {
                return $this->Customername;
    }
    public function getcustomeremail()
    {
                return $this->email;
    }
    public function getcustomerphone()
    {
                return $this->phone;
    }

    public function displayCustomer()
    {
        echo "\n=========Customer Details=======\n";
        echo "Customer Id: ".$this->Customerid. "\n";
        echo "name: ".$this->Customername. "\n";
        echo "email: ".$this->email. "\n";
        echo "phone: ".$this->phone. "\n";
        echo "\n";
    }
}
?>