<?php
class Task
{
    private $Id;
    private $Title;
    private $Status;

    public function __construct($Id,$Title)
    {
        $this->Id=$Id;
        $this->Title=$Title;
        $this->Status="Pending";
    }

    public function getId()
    {
        return $this->Id;   
    }

    public function getTitle()
    {
        return $this->Title;
    }

    public function getStatus()
    {
        return $this->Status;
    }

    public function MarkasCompleted()
    {
            return $this->Status="Completed";
    }

    public function updateTitle($Title)
    {
        return $this->Title=$Title;
    }

}
?>