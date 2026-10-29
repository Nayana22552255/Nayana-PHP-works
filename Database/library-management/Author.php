<?php

class Author
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function addAuthor(string $name,?string $biography=null,):void
    {
            $sql="INSERT INTO authors(author_name, biography)
                VALUES(:name, :biography)";

            $stmt=$this->conn->prepare($sql);

            $stmt->execute([
                            ':name'=>$name,
                            ':biography'=>$biography,
                           
                         ]);

            echo "Author added successfully\n";
            //echo "Author Id:".$this->conn->lastInsertedId(). "\n";

    }

    public function showAll():void
    {
            $sql="SELECT author_id,author_name,biography FROM authors ORDER BY author_id";
            $stmt=$this->conn->query($sql);

            $authors=$stmt->fetchAll(PDO::FETCH_ASSOC);

            if(empty($authors))
                {
                    echo "No authors found.\n";
                    return;
                }
            foreach($authors as $author)
                {
                    echo "----------------------\n";
                    echo "Author ID: ".$author['author_id'], "\n";
                    echo "Author Name: ".$author['author_name']. "\n";
                    echo "Biography: ".($author['biography'] ?? 'Not available'). "\n";
                }
    }

    public function search(string $keyword):void
    {
            $sql="SELECT author_id,author_name,biography FROM authors
            WHERE author_name LIKE :keyword ORDER BY author_name";

            $stmt=$this->conn->prepare($sql);

            $stmt->execute([
                ':keyword'=>'%'.$keyword. '%'
            ]);

            $authors=$stmt->fetchAll(PDO::FETCH_ASSOC);

            if(empty($authors))
                {
                    echo "No matching authors found.\n";
                    return;
                }

                foreach($authors as $author)
                    {
                        echo "Author Id:".$author['author_id']. "\n";
                        echo "Autor name:".$author['author_name']. "\n";
                        echo "Biography: ".($author['biography']?? 'Not avilable'). "\n";
                        echo "-------------------------------------------------------\n";
                    }


    }

    public function update(int $authorId, string $name, ?string $biography = null, ):void
    {
        $sql="UPDATE authors SET author_name=:name,  biography=:biography WHERE author_id=:id";

        $stmt=$this->conn->prepare($sql);
        $stmt->execute([
            ':name'=>$name,
            ':biography'=>$biography,
            ':id'=>$authorId
        ]);

        if($stmt->rowCount()>0)
            {
                echo "Author updated successfuly\n";
            }
            else{
                echo "No author updated .check the id or details\n";

            }


    }

   /* public function showAuthorsWithBooks(): void
    {
    $sql = "SELECT
                a.author_id,
                a.author_name,
                a.biography,
                GROUP_CONCAT( b.title SEPARATOR ', ') AS book_names
                FROM authors a
                LEFT JOIN book_authors ba
                ON a.author_id = ba.author_id
                LEFT JOIN books b
                ON ba.book_id = b.book_id
                GROUP BY
                a.author_id,
                a.author_name,
                a.biography
                ORDER BY a.author_id";

    $stmt = $this->conn->query($sql);
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($authors)) {
        echo "No authors found.\n";
        return;
    }

    foreach ($authors as $author) {
        echo "-------------------------\n";
        echo "Author ID: " . $author['author_id'] . "\n";
        echo "Author Name: " . $author['author_name'] . "\n";
        echo "Biography: " . ($author['biography'] ?? 'Not available'). "\n";
        echo "Book Name(s): ". ($author['book_names'] ?? 'No books assigned') . "\n";
    }
    }*/

    public function delete(int $authorId):void
    {
        $check=$this->conn->prepare("SELECT COUNT(*) FROM book_authors WHERE author_id=:id");
        $check->execute([':id'=>$authorId]);

        if((int)$check->fetchColumn()>0) 
            {
                echo "cannot delete thid author becuase they are associated with one or more books\n";
                return;
            }

        $stmt=$this->conn->prepare("DELETE FROM authors WHERE author_id=:id");
        $stmt->execute([':id'=>$authorId]);

        if($stmt->rowCount()>0) 
            {
                echo "Author deleted successfully.\n";
            }
            else{
                echo "Author Id not fount.\n";
                
            }
    }

}
?>