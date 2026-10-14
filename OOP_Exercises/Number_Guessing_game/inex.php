<?php
require_once("numbergame.php");


echo"-------Game Guessing----";
echo "\n";
$game= new Game();

while(true){

    $guess=readline("enter the number\n");
    $result= $game->checkguess($guess);
    echo $result."\n";
   if ($result == "Correct!") {
        echo "Congratulations! You guessed the number.\n";
        break;
    }

}
?>