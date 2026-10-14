<?php
require_once("Agecalc.php");

$calculator=new Agecalculator();
$dateofbirth=readline("enter the date of birth in (yyyy-mm-dd)");

$calculator->calculateage($dateofbirth);


?>