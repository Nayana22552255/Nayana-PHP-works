<?php
//dependency inversion principle
interface Database
{
    public function save();
}
class Mysqldatabase implements Database
{
    public function save()
    {
        echo " saving data to Mysql\n";
    }
}
class Student 
{
    private Database $database;
    public function __construct(Database $database)
    {
        $this -> database=$database;
    }

    public function savestudent()
    {
        $this -> database ->save();
    }
}
$database=new Mysqldatabase();
$student=new Student($database);
$student->savestudent();
