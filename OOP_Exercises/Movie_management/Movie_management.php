<?php
require_once("Movie.php");

class Moviemangement
{
    public $movies=[];

    public function Addmovie(Movie $movie)
    {
            $this->movies[]= $movie;
            echo "\n Movie added successfully\n";
    }
    public function displaymovies()
    {
        if(empty($this->movies))
            {
                echo "\nNo movies available\n";
            }

            echo "\n============Movie  List=========\n";
            foreach( $this->movies as $movie)
                {
                    $movie->Displaymovie();
                }
    }

    public function searchmovie($searchtitle)
    {
        $found= false;

        foreach($this->movies as $movie){

            if(strtolower($movie->gettitle())==strtolower($searchtitle))

            {
                    echo "\n Movie Found\n";
                    $movie->Displaymovie();

                    $found = true;
                    break;
            }
    }
    if(!$found)
        {
            echo "\nmovie is not found\n";
        }
    
    }
}

?>
