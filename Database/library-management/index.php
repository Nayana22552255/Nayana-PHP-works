<?php
require_once "Database.php";
require_once "Book.php";
require_once "Author.php";
require_once "Genre.php";
require_once "Member.php";
require_once "Borrowing.php";
require_once "Fee.php";
require_once "Bookinventory.php";

try
{
    $database= new Database();
    $conn=$database->connect();
    $book=new Book($conn);
    $author=new Author($conn); 
    $genre=new Genre($conn);
    $member=new Member($conn);
    $borrowing=new Borrowing($conn);
    $fee=new Fee($conn);
    $fee->processOverdueBorrowings();
    $bookinventory=new BookInventory($conn);
    $borrowing->updateOverdueLateFees();

    echo "==============================\n";
    echo "     LIBRARY MANGEMENT SYSTEM \n";
    echo "==============================\n";

    try {
        $fee->processOverdueBorrowings();
    } catch (Throwable $e) {
        echo "Error processing automatic overdue fees: " . $e->getMessage() . "\n";
    }

    $sections = [
        1 => [
            'title' => 'Book Management',
            'options' => [
                1 => ['label' => 'Display All Books', 'action' => 1],
                2 => ['label' => 'Add a New Book', 'action' => 2],
                3 => ['label' => 'Search a Book', 'action' => 3],
                4 => ['label' => 'Update a Book', 'action' => 4],
                5 => ['label' => 'Delete a Book', 'action' => 5]
            ]
        ],
        2 => [
            'title' => 'Author Management',
            'options' => [
                1 => ['label' => 'Add Author', 'action' => 6],
                2 => ['label' => 'Display All Authors', 'action' => 7],
                3 => ['label' => 'Search for an Author', 'action' => 8],
                4 => ['label' => 'Update an Author', 'action' => 9],
                5 => ['label' => 'Delete an Author', 'action' => 10]
            ]
        ],
        3 => [
            'title' => 'Genre Management',
            'options' => [
                1 => ['label' => 'Add Genre', 'action' => 11],
                2 => ['label' => 'Display All Genres', 'action' => 12],
                3 => ['label' => 'Search Genres', 'action' => 13],
                4 => ['label' => 'Update Genre', 'action' => 14],
                5 => ['label' => 'Delete Genre', 'action' => 15]
            ]
        ],
        4 => [
            'title' => 'Member Management',
            'options' => [
                1 => ['label' => 'Add a Member', 'action' => 16],
                2 => ['label' => 'Display All Members', 'action' => 17],
                3 => ['label' => 'Search for a Member', 'action' => 18],
                4 => ['label' => 'Update a Member', 'action' => 19],
                5 => ['label' => 'Set Member Status', 'action' => 20],
                6 => ['label' => 'Delete a Member', 'action' => 21]
            ]
        ],
        5 => [
            'title' => 'Borrowing Management',
            'options' => [
                1 => ['label' => 'Display All Borrowing Records', 'action' => 22],
                2 => ['label' => 'Borrow a Book', 'action' => 23],
                3 => ['label' => 'Return a Book', 'action' => 24],
                4 => ['label' => 'View All Fines of a Member', 'action' => 30],
                5 => ['label' => 'View Member Borrowing History', 'action' => 31]

            ]
        ],
        6 => [
            'title' => 'Fees Management',
            'options' => [
                1 => ['label' => 'Show All Fees', 'action' => 25],
                2 => ['label' => 'Search for a Fee', 'action' => 26],
               // 3 => ['label' => 'Add a Late Fee', 'action' => 27],
                4 => ['label' => 'Report Lost Book and Add Fee', 'action' => 28],
                5 => ['label' => 'Mark Fee as Paid', 'action' => 29],
                3=> ['label' => 'View All Fines and Revenue', 'action' => 32],

            ]
        ],
        7 => [
            'title'=>'Book History',
            'options'=>[
                1=>['label'=>'Menu','action'=>33]
            ]
        ]];
        

    while (true) {
        
        $borrowing->updateOverdueLateFees();
        echo "\n========== MAIN MENU ==========\n";
        foreach ($sections as $sectionId => $section) {
            echo $sectionId . ". " . $section['title'] . "\n";
        }
        echo "0. Exit\n";

        $sectionChoice = (int) trim(readline("Choose a management section: "));
        if ($sectionChoice === 0) {
            break;
        }
        if (!isset($sections[$sectionChoice])) {
            echo "Invalid section. Please select 0-6.\n";
            continue;
        }

        $selectedSection = $sections[$sectionChoice];
        echo "\n========== " . strtoupper($selectedSection['title']) . " ==========\n";
        foreach ($selectedSection['options'] as $optionId => $option) {
            echo $optionId . ". " . $option['label'] . "\n";
        }
        echo "0. Back to Main Menu\n";

        $optionChoice = (int) trim(readline("Choose an option: "));
        if ($optionChoice === 0) {
            continue;
        }
        if (!isset($selectedSection['options'][$optionChoice])) {
            echo "Invalid option. Please choose one of the listed options.\n";
            continue;
        }

        $choice = $selectedSection['options'][$optionChoice]['action'];

    try {
    } catch (Throwable $e) {
        echo "Error processing automatic overdue fees: " . $e->getMessage() . "\n";
    }

    try
    {
        switch($choice)
        {
            case 1:
                        $book->showAll();
                        echo "------------------\n";
                        break;
            case 2:
                        $title=trim(readline("Enter book title: "));

                        while (true) {
                            $yearInput=trim(readline("Enter publication year: "));
                            break;
                        }

                        $year= $yearInput === "" ? null : (int)$yearInput;

                        $isbn=trim(readline("Enter ISBN (leave blank if unavailable): "));
                        $isbn=$isbn === "" ? null : $isbn;

                        $description=trim(readline("Enter description: "));

                        $description= $description === "" ? null : $description;

                        $price=(float)readline("Enter book price: ");
                        $copies = (int) readline("Enter the number of copies: ");

                        if ($copies < 1) {
                            echo "Number of copies must be at least 1.\n";
                            break;
                        }

                        $book->addBook(
                            $title,
                            $year,
                            $isbn,
                            $description,
                            $price,
                            $copies
                        );
                        echo "--------------------------\n";
                        break;

            case 3:
                        $keyword=trim(readline("Enter book title or keyword: "));
                        $book->search($keyword);
                        echo "-----------------------------\n";
                        break;
                        
            case 4:
                        $book_id=(int)readline("enter book id: ");
                        $book->updateBook($book_id);
                        echo "------------------------------\n";
                        break;
            case 5:
                        $book_id=(int)readline("enter the book Id to delete: ");
                        $book->delete($book_id);
                        echo "------------------------------------\n";
                        break;
                    
            case 6:
                        $name=readline("Enter the author name: ");
                        $title=readline("Enter the title of the book");
                        $biography=readline("enter the Biography: ");
                        $biography=$biography === "" ? null : $biography;
                        $author->addAuthor($name,$biography);
                        echo "-------------------------------------------\n";
                        break;
            case 7:
                        $author->showAll();
                        //$author->showAuthorsWithBooks();
                        echo "----------------------------------------------\n";
                        break;

            case 8:     $keyword=trim(readline("Enter the author name or keyword: "));
                        $author->search($keyword);
                        echo "--------------------------------------------------\n";
                        break;

            case 9:     $authorId=(int)readline("enter the id: ");
                        $name=trim(readline("enter the author name: ")); 
                        $biography=trim(readline("enter the biography: "));
                        $biography=$biography === "" ? null : $biography;
                        $author->update($authorId,$name,$biography);
                        echo "---------------------------------------------------\n";
                        break;
            case 10:
                        $authorId=(int)readline("enter the author id: ");
                        $author->delete($authorId);
                        echo "---------------------------------------------------\n";
                        break;
            case 11:
                        $name=trim(readline("enter the genre name: "));
                        $parentIdInput=trim(readline("enter the parent id, or leave blank for none: "));
                        $parentId=$parentIdInput === "" ? null : (int)$parentIdInput;
                        $genre->add($name,$parentId);
                        echo "---------------------------------------------------\n";
                        break;

            case 12:    $genre->showAll();
                        echo "---------------------------------------------------\n";
                        break;

            case 13:    $keyword=readline("enter the keyword for searching the genre: ");
                        $genre->search($keyword);
                        echo "---------------------------------------------------\n";
                        break;

            case 14:    $genreId=(int)readline("enter the genre id: ");
                        $name=trim(readline("enter the genre name: "));
                        $parentIdInput=trim(readline("enter the parent id, or leave blank for none: "));
                        $parentId=$parentIdInput === "" ? null : (int)$parentIdInput;
                        $genre->update($genreId,$name,$parentId);
                        echo "---------------------------------------------------\n";
                        break;

            case 15:    $genreId=(int)readline("enter the genreId: ");
                        $genre->delete($genreId);
                        echo "---------------------------------------------------\n";
                        break;

            case 16:
                        $name=readline("Enter the member name: ");
                        $email=readline("Enter the email: ");
                        $phone=readline("Enter the phone: ");
                        $lateFeeRateForCategory=trim(readline("Enter category (staff, Undergraduate, Post Graduate, Research): "));
                        $maxBooks=(int)readline("Enter the maximum books: ");
                        $loanDays=(int)readline("Enter the loan days: ");
                        $member->addMember($name,$email,$phone,$lateFeeRateForCategory,$maxBooks,$loanDays);
                        echo "---------------------------------------------------\n";
                        break;
                        
            case 17:    $member->showAll();
                        echo "---------------------------------------------------\n";
                        break;
                    
            case 18:
                        $keyword=readline("Enter the keyword for searching\n");
                        $member->searchMember($keyword);
                        echo "---------------------------------------------------\n";
                        break;
            

            case 19:    $memberId=(int)readline("Enter the member Id: ");
                        $name=readline("Enter the Name: ");
                        $email=readline("enter a email: ");
                        $phone=readline("enter the phone number: ");
                        $lateFeeRateForCategory=trim(readline("Enter category (staff, Undergraduate, Post Graduate, Research): "));
                        $maxBooks=(int)readline("Enter the Maximum books: ");
                        $loanDays=(int)readline("Enter the loan Days");
                        $member->updateMembers($memberId,$name,$email,$phone,$lateFeeRateForCategory,$maxBooks,$loanDays,);
                        echo "---------------------------------------------------\n";
                        break;
                       
            case 20:
                        $memberId=(int)readline("Enter the members id: ");
                        $status=strtolower(trim(readline("Enter status (active/inactive): ")));
                        if($status !== 'active' && $status !== 'inactive')
                        {
                            echo "Invalid status. Enter active or inactive.\n";
                            break;
                        }
                        $active=$status === 'active';
                        $member->setStatus($memberId,$active);
                        echo "---------------------------------------------------\n";
                        break;

            case 21:
                        $memberId=(int)readline("Enter the member id for deleting: ");
                        $member->deleteMember($memberId);
                        echo "---------------------------------------------------\n";
                        break;

            case 22:    $borrowing->showAll();
                        echo "---------------------------------------------------\n";
                        break;

            case 23:    $memberId=(int)readline("enter the memeber id: ");
                        $bookId=(int)readline("enter the book id: ");
                        $borrowing->borrow($memberId,$bookId);
                        echo "---------------------------------------------------\n";
                        break;

            case 24:    $borrowingId=(int)readline("Enter the borrowing id: ");
                        $borrowing->returnBook($borrowingId);
                        echo "---------------------------------------------------\n";
                        break;

            case 25:
                        $fee->showAll();
                        echo "---------------------------------------------------\n";
                        break;
                
            case 26:
                        $keyword=readline("enter a keyword for searching a fee: ");
                        $fee->search($keyword);
                        echo "---------------------------------------------------\n";
                        break;
            
            /*case 27:
                        $borrowingId=(int)readline("enter the Id: ");
                        $fee->addLateFee($borrowingId);
                        break;*/

            case 28:
                        $borrowingId=(int)readline("Enter the borrowing id: ");
                        $fee->addLostFee($borrowingId);
                        echo "---------------------------------------------------\n";
                        break;

            case 29:
                        $feeId=(int)readline("Enter the fee id: ");
                        $fee->markPaid($feeId);
                        echo "---------------------------------------------------\n";
                        break;
            
            /*case 30:
                        $feeId=(int)readline("Enter the fee id: ");
                        $fee->waiveFee($feeId);
                        break;*/

            case 30:
                        $memberId = (int) readline("Enter the member ID: ");
                        $borrowing->viewAllMemberFines($memberId);
                        echo "---------------------------------------------------\n";
                        break;

            case 31:
                        $memberId=(int)readline("Enter Member ID: ");
                        $borrowing->showMemberBorrowingHistory($memberId);
                        echo "---------------------------------------------------\n";
                        break;

            case 32:
                        $fee->viewAllFines();
                        echo "---------------------------------------------------\n";
                        break; 

            case 33:
                        $bookinventory->menu();
                        break;
                
            default:
                        echo "Invalid choice. Please select 1-21.\n";
                        break;

        }
    }catch(Throwable $e)
    {
        echo "Operation Failed: ".$e->getMessage()."\n";
    }

    }

}catch(Throwable $e)
{
    echo "Database connection failed". $e->getMessage(). "\n";
}
