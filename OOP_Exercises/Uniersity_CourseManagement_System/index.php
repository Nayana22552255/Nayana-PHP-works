<?php
require_once("University.php");

$university=new University();
echo "===========University Course Management System=============\n";
$university->displaystudents();
$university->displayinstructor();
$university->displaycousres();
$university->diplayregistrations();

?>