<?php

require_once("user.php");

 $user = new User();

 echo "====================\n";
 echo "Login System \n";

 $username=readline("enter the user name");
 $password= readline("enter the password");

if($user->login($username, $password ))
    {
        echo "Login Successful\n";
    }
else{
    echo "Login failed\n";
    echo "enter the correct username and password\n";
}

?>
