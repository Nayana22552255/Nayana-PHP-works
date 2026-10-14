<?php
require_once("Wordguessing.php");

$game= new Wordguessing();

$game->selectedword();
$game->hiddenword();

while(true)
    {
        $game->displayword();
        $game->displayattempts();
        $letter=$game->getplayerguess();
        $game->checkletter($letter);

        if($game->iswon())
            {
                echo "you won the game \n";
                $game -> displayallword();
                break;
            }

        if($game->isgameover())
            {
                echo "Game over \n";
                $game -> displayallword();
                break;
            }

    }

?>