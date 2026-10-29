<?php
 class Genre 
 {
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function add(string $name, ?int $parentId=null):void 
    {
        try
        {
            if($parentId !== null)
                {
                    $check=$this->conn->prepare("SELECT genre_id FROM genres WHERE genre_id=:id");
                    $check->execute([':id' =>$parentId]);

                    if(!$check->fetch()) 
                        {
                            echo "Parent genre Id does not exist.\n";
                            return;
                        }
                }

            $sql="INSERT INTO genres(genre_name,parent_genre_id)
                    VALUES(:name,:parent_id)";

            $stmt=$this->conn->prepare($sql);
            $stmt->execute([
                    ':name'=>$name,
                    ':parent_id'=>$parentId
            ]);

            echo "Genre added successfully\n";

        }catch(PDOException $e)
        {
            echo "Error adding Genre: ". $e->getMessage(). "\n";
        }
    }

    public function showAll()
    {
        try{
            $sql= "SELECT  g.genre_id,
                           g.genre_name,
                           g.parent_genre_id,
                           p.genre_name AS parent_name FROM genres g
                           LEFT JOIN genres p
                           ON g.parent_genre_id=p.genre_id ORDER BY g.genre_id";
            $stmt=$this->conn->query($sql);
            $genres=$stmt->fetchAll(PDO::FETCH_ASSOC);

            if(empty($genres))
                {
                    echo "No genres found \n";
                    return;
                }

            echo "\n------------------Genre list-----------\n";
            foreach($genres as $genre)
                {
                    echo "Genre Id: ".$genre['genre_id']. "\n";
                    echo "Genre Name: ".$genre['genre_name']. "\n";

                    if($genre['parent_genre_id']=== null)
                        {
                            echo "Parent Genre : None\n";
                        }
                    else{
                            echo "Parent Genre: ".$genre['parent_name']. "\n";
                    }
                }
        }catch(PDOException $e)
        {
            echo "Error Displaying genres: ".$e->getMessage(). "\n";
        }
    }

    public function search(string $keyword):void 
    {
        try{
            $sql="SELECT genre_id,genre_name,parent_genre_id FROM genres
                    WHERE genre_name LIKE :keyword ORDER BY genre_name ";

            $stmt=$this->conn->prepare($sql);

            $stmt->execute([
                    ':keyword'=>'%'. $keyword . '%']);
            $genres=$stmt->fetchAll(PDO::FETCH_ASSOC);

            if(empty($genres))
                {
                    echo "No matching genres found\n";
                    return;
                }

            foreach($genres as $genre)
                {
                    echo "Genre Id: ".$genre['genre_id']. "\n";
                    echo "Gnere Name: ".$genre['genre_name']. "\n";
                    echo "Parent Genre Id: ".($genre['parent_genre_id']?? 'None'). "\n";
                    echo "---------------------------------------------------------\n";

                }
        }catch(PDOException $e)
        {
            echo "Error serching genres: ".$e->getMessage(). "\n";
        }
    }

    public function update(int $genreId,string $name,?int $parentId=null ):void 
    {
        try 
        {
            if($parentId == $genreId)
                {
                    echo "A genre cannot be its own parent.\n";
                    return;
                }
            $check= $this->conn->prepare("SELECT genre_id FROM genres WHERE genre_id =:id");

            $check->execute([':id'=>$genreId]);

            if(!$check->fetch())
                {
                    echo "Genre Id not found\n";
                    return;
                }
            if($parentId != null)
                {
                    $checkParent=$this->conn->prepare("SELECT genre_id FROM genres WHERE genre_id= :id");

                    $checkParent->execute([':id'=>$parentId]);

                    if(!$checkParent->fetch())
                        {
                            echo "Parent genre Id does not exist\n";
                            return;
                        }
                }

                $sql="UPDATE genres SET genre_name=:name, parent_genre_id=:parent_id
                        WHERE genre_id=:id";

                $stmt=$this->conn->prepare($sql);

                $stmt->execute([':name'=>$name,
                                ':parent_id'=>$parentId,
                                ':id'=>$genreId]);

                echo "Genre updated successfully\n";

        }catch(PDOException $e)
        {
            echo "Error updateing genre: ". $e->getMessage(). "\n";

        }
    }   

    public function delete(int $genreId):void 
    {
        try 
        {
            $checkBooks=$this->conn->prepare("SELECT COUNT(*)FROM book_genres
                        WHERE genre_id=:id");

            $checkBooks->execute([':id'=>$genreId]);

            if((int)$checkBooks->fetchColumn()>0) 
                {
                    echo "Cannot delete this genre becuase it is assigned to books.\n";
                    return;
                }

            $checkChildren=$this->conn->prepare("SELECT COUNT(*)FROM genres WHERE parent_genre_id=:id");

            $checkChildren->execute([':id'=>$genreId]);

            if((int) $checkChildren->fetchColumn()>0) 
                {
                    echo "Cannot delte this genre because other genre use it as their parent.\n";
                    return;
                }

            $stmt=$this->conn->prepare("DELETE FROM genres WHERE genre_id= :id");

            $stmt->execute([':id'=>$genreId]);

            if($stmt->rowCount()>0) 
                {
                    echo "Genre delete successfully\n";

                }else{
                    echo "Genre Id not fount\n";

                }
        }catch(PDOException $e)
        {
            echo "Error deleting Genre.". $e->getMessage(). "\n";

        }
    }
 }

?>