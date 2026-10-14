<?php
class Member
{
    private $membername;
    private $memberid;

    public function __construct($membername,$memberid)
    {
        $this->membername=$membername;
        $this->memberid=$memberid;
    }

    public function getMemebername()
    {
        return $this->membername;
    }
     public function getMemeberid()
    {
        return $this->memberid;
    }

    public function displaymember()
    {
        echo "Member Id: ".$this->memberid."\n";
        echo "Member Name: ".$this->membername."\n";
        
    } 
    public function toarray()
    {
        return [
            "memberid"=>$this->memberid,
            "membername"=>$this->membername
        ];
    }
}
?>