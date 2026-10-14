<?php
require_once("Printable.php");

abstract class Person implements Printable
{
   protected $id;                               //can access inside the calss and inside its child class 
   protected $name;
   protected $email;


   public function __construct($id, $name, $email)
   {
            $this->id=$id;
            $this->name=$name;
            $this->email=$email;
   }

   public function getId()
   {
    return $this->id;
   }

   public function getName()
   {
    return $this->name;
   }

   public function getEmail()
   {
    return $this->email;
   }

    abstract public function getRole();         //implementing polymorphism 
   
}
?>