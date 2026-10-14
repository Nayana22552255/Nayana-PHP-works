<?php
class Number
{
    private $row;

    public function __construct($row)
    {
        $this->row=$row;
    }

    public function display()
    {
        for($i=1; $i<= $this->row; $i++)
            {
                for($j=1; $j<=$this->row; $j++)
                    {
                        echo " ";
                    }
                for($k=1; $k<=$i; $k++)
                    {
                        echo $k. " ";
                    }
                    
            echo "\n";
            }

    }
}

$no=new Number(5);
$no->display();
?>