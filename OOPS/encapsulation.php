<?php
class Myclass{
    private $secret="top secret";

    private function getsecret()
{
   return $this->secret;

}
public function revealsecret()
{
    return  $this->getsecret();
}
    
}

$obj= new Myclass();
echo $obj->revealsecret();
echo $obj->secret;
echo $obj->getsecret;
?>
