<?php
class Rentalmanager
{
    private $rentals=[];

    public function Rentvehicle($rentalid, Customermanager $customer, Vehicle $vehicle,$days,$rateperday)
    {
            if(!$vehicle->isavailable())
                {
                    echo "Vehicle is already rented.\n";
                    return;
                }
            $vehicle->Rent();

            $rental= new rental(
                $rentalid,
                $customer,
                $vehicle,
                $days,
                $rateperday
            );

        $this->rentals[]=$rental;
        echo "vehicle rented successfully\n";
        echo "Total Rent: \n";
        echo $rental->calculaterent(). "\n";
    }


    public function Returnvehicle($rentalid)
    {
        foreach($this->rentals as $rental)
            {
                if($rental->getrentalid() == $rentalid)
                    {
                        $rental->returnVehicle();
                        echo "Vehicle returend successfully\n";
                        return;
                    }
            }
            echo "rental not found\n";
    }

    public function Rentalhistory()
    {
            if(empty($this->rentals))
                {
                    echo "No rental history \n";
                    return;
                } 
            foreach($this->rentals as $rental)
                {
                    echo "\n";
                    $rental->displayrental();
                }  
    }
}