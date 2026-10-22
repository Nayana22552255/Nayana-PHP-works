<?php


    $host="localhost";
    $dbname="college_db";
    $username="root";
    $password="root";

    try
    {
        
        //create the datbase connection
        $pdo=new PDO(
            "mysql:host=$host;
             dbname=$dbname;
             charset=utf8mb4",
             $username,
             $password
        );

        //configure the PDO errors

        $pdo->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        echo "Data base connected successfully!\n";

        //$sql="INSERT INTO college(cname,place,post)
                           // VALUES(:cname,:place,:post)";

        $sql= "UPDATE college SET post=:post, pin=:pin WHERE cid=:cid";

        $stmt=$pdo->prepare($sql);

        while(true)
            {
                //$cname=readline("enter the college name: ");
                //$place=readline("enter the place: ");
                $cid=(int)readline("enter the college id:");
                $post=readline("enter the post:");
                $pin=(int)readline("enter the pin: ");

                $stmt->execute([
                    //':cname'=>$cname,
                    //':place'=>$place,
                    ':cid'=>$cid,
                    ':post'=>$post,
                    ':pin'=>$pin
                ]);

                echo "the college details entered successfully\n";

                $choice=strtolower(readline("Add another college? (yes/no):"));

                if($choice != "yes")
                    {
                        break;
                    }
                echo "\n";

         }
    
    }catch(PDOException $e)
    {
        echo "Database error: ". $e->getMessage(). "\n";
    }