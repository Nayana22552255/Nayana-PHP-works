<?php
require_once("Library.php");

$library= new Library();

echo "\n";
while(true){
echo "\n";
echo "--------LIBRARY SYSTEM------------- \n";
echo "1. ADD  BOOK\n";
echo "2. VIEW BOOK\n";
echo "3. ADD  MEMBER\n";
echo "4. VIEW MEMBER\n";
echo "5. ISSUE BOOK\n";
echo "6. RETURN BOOK\n";
echo "7. EXIT\n";
echo "\n";

$choice= readline("enter your choice: ");
switch($choice){
    case 1:
        $bookid=readline("enter the book ID: ");
        $bookname=readline("enter the book name: ");
        $authorname=readline("enter the author name: ");

        $book= new Book(
            $bookid,
            $bookname,
            $authorname
        );
        $library->addBook($book);
        echo "\n";
        break;

        case 2:
            $library->displaybook();
            //echo "\n";
            break;

        case 3:
            $memberid=readline("enter the member ID: ");
            $membername=readline("enter the member name: ");

            $member= new Member(
                $memberid,
                $membername
            );
            $library->addMembers($member);
            echo "\n";
            break;

        case 4: 
            echo "\n";
            $library->displaymember();
            echo "\n";
            break;
        
        case 5:
            $bookid=readline("enter the BookId: ");
            $memberid=readline("enter the memberId: ");

            $library->issuebook(
                $bookid,
                $memberid
            );
            echo "\n";
            break;

        case 6:
            $bookid=readline("enter the bookID: ");

            $library ->returnbook($bookid);
            echo "\n";
            break;

        case 7:
            echo "EXIT ";
            echo "\n";
            exit;

    default:
                echo "The Library System not Worked Properly";

}

}
?>