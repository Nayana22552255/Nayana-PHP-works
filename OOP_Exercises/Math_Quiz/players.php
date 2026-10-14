<?php
class players
{
    private $name;
    private $answer;

    public function setname($name)
    {
        $this->name=$name;
    }
    public function getname()
    {
        return $this->name;
    
    }
    public function setanswer($answer)
    {
        $this->answer=$answer;
    }
    public function getanswer()
    {
        return $this->answer;
    }
}