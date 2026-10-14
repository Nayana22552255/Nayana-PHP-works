<?php
class employee
{
    public $name;
    public $employeeid;

    public function __construct($name,$employeeid)
    {
        $this->name=$name;
        $this->employeeid=$employeeid;
    }
    public function displayemployee()
    {
        echo "employee name is:".$this->name."\n";
        echo"employeeid is:".$this->employeeid."\n";
    }
}
class developer extends employee
{
    public $programinglanguage;
    public function __construct($name,$employeeid,$programinglanguage)
    {
        parent ::__construct($name,$employeeid);
        $this->programinglanguage = $programinglanguage;
    }
    public function displaydeveloper()
    {
        
    echo "developer details:\n";
    echo "programming languge:".$this->programinglanguage."\n";
        $this->displayemployee();
    }
}
class manger extends employee
{
    public $teamsize;
    public function __construct($name,$employeeid,$teamsize)
    {
        parent ::__construct($name,$employeeid);
        $this->teamsize=$teamsize;

    }
    public function displaymanager()
    {
     echo "manger details:\n";   
    echo " team size:".$this->teamsize."\n";
    $this->displayemployee();
    
    }
}
$developer= new developer("nayana",101,"php");
//echo "developer deatils:";
$developer->displaydeveloper();

$manager=new manger("anu",102,5);
//echo "manger details:";
$manager->displaymanager();
?>
