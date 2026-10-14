<?php
require_once("Movie_management.php");

$moviemanage = new Moviemangement();
while(true)
    {
        echo "\n======Movive Management=======\n";
        echo "1.Add Movie \n";
        echo "2.Display Movie \n";
        echo "3.Search Movie \n";
        echo "4.Exit\n";

        $choice = readline("enter your choice");

        switch($choice)
        {
            case 1: 
                $title=readline("enter the movie title: ");
                $releasedyear=readline("enter the releesed date: ");
                $directorname=readline("enter the director name: ");
                $language=readline("enter the language: ");
                $movierating=readline("movie rating out of 10: ");

                $movie= new Movie($title,$releasedyear,$directorname,$language,$movierating);
                $moviemanage->Addmovie($movie);
                break;

            case 2:
                $moviemanage->displaymovies();
                break;

            case 3:
                $searchtitle= readline("enter the movie title to search: ");
                $moviemanage->searchmovie($searchtitle);
                break;
            case 4:
                echo "\n Thankyou for using the movie management System\n";
                exit;

            default:
                echo "\n Invalid choice please try again\n";


        }
    }


?>