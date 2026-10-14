<?php
class Movie
{
    public $moviename;
    public $releasedyear;
    public $director;
    public $language;
    public $movierating;
    

    public function __construct($moviename,$releasedyear,$director,$language,$movierating)
    {
        $this->moviename=$moviename;
        $this->releasedyear=$releasedyear;
        $this->director=$director;
        $this->language=$language;
        $this->movierating=$movierating;
        
    }
    public function gettitle()
    {
        return $this->moviename;
    }

    
    public function Displaymovie()
    {
        echo "TITLE: ".$this->moviename. "\n";
        echo "RELEASED YEAR: ".$this->releasedyear. "\n";
        echo "DIRECTOR NAME: ".$this->director. "\n";
        echo "LANGUAGE: ".$this->language. "\n";
        echo "MOVIE RATING: ".$this->movierating. "\n";

    }


}
?>