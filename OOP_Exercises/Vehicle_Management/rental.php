<?php
class rental
{
    private $rentalid;
    private $customer;
    private $vehicle;
    private $days;
    private $rateperday;
    private $totalamount;
    private $status;

                                           //typse declartion
    public function __construct($rentalid,Customermanager $customer,Vehicle $vehicle,$days,$rateperday)
    {
        $this->rentalid=$rentalid;
        $this->customer=$customer;
        $this->vehicle=$vehicle;
        $this->days=$days;
        $this->rateperday=$rateperday;

        $this->totalamount=$day * $rateperday;
        $this->status="Active";
    }


    public function getrentalid()
    {
        return $this->rentalid;
    }
    public function getCustomer()
    {
        return $this->customer;
    }
    public function getvehicle()
    {
        return $this->vehicle;
    }
    public function calculaterent()
    {
        return $this->days * $this->rateperday;
    }
    public function returnVehicle()
    {
        $this->vehicle->returnVehicle();
        $this->status="Returned\n";
    }
    public function displayrental()
    {
        echo "=======Rental Details======\n";
        echo "Rental Id: ".$this->rentalid. "\n";
        echo "Customer: ".$this->customer->getcustomername(). "\n";
        echo "Vehicle: ".$this->vehicle->getBrand(). " ".
                        $this->vehicle->getModel(). "\n";
        echo "Days: ".$this->days. "\n";
        echo "Rate per day: ".$this->rateperday. "\n";
        echo "Total amount: ".$this->totalamount. "\n";
        echo "Status: ".$this->status. "\n";
    }
}