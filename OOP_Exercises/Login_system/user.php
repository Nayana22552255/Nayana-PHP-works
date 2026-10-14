<?php
 class User
 {
    private $username="admin";
    private $password="1234";

    public function login($username, $password)
    {
        if($username === $this->username && $password === $this->password)
            {
                return true;

            }
        else
            {
                return false;
            }
    }
 }
 ?>