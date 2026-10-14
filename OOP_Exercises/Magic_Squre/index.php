<?php

require_once("Game.php");

echo "=====================\n";
echo "    Magic Square\n";
echo "=====================\n";

$name = trim(readline("Enter your name: "));

$game = new Game($name);

$game->Sartgame();

?>