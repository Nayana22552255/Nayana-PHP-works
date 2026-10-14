<?php
require_once("Book.php");
require_once("Member.php");

class Library
{
    private $books= [];         //creating a books array
    private $members= [];       //creating the member array 
    private $datafile="Data.json";

    public function __construct()
    {
        $this->loadData();
    }

   
   
   private function saveData()
   {
    $data=[
        "books"=>[],
        "members"=>[]
    ];

    foreach($this->books as $book){
        $data["books"][]=$book->toarray();
    }
    foreach($this->members as $member){
        $data["members"][]=$member->toarray();
    }
    $jsonData=json_encode($data,JSON_PRETTY_PRINT);             //converts the php array into json
    file_put_contents($this->datafile,$jsonData);               //write the json 
   }


   private function loadData()
   {
        if(!file_exists($this->dataFile)){
            return;
        }
        $jsonData=file_get_contents(
            $this->dataFile
        );

        $data=json_decode($jsonData,true);

        foreach($data["books"] as $bookData){
            $book =new Book(
                $booKData["bookid"],
                $bookData["title"],
                $bookData["author"]
            );

            if(!$bookDta["isAvailable"]){
                $book->issueBook();
            }
            $this->books[]=$book;

        }
        foreach($data["members"] as $memberData){
            $member= new Member(
                $memberData["memberid"],
                $memberData["name"]

            );
        }

        $this->members[]=$member;
   }


    public function addBook(Book  $book)        //Add a book
    {
            $this->books[]=$book;
            echo "Book added successfully \n";
            
    }

    public function addMembers(Member $member)      //Add a member
    {
            $this->members[]=$member;
            echo "Members added Successfully";
    }
    public function displaybook()       //Display all book
    {
       if(empty($this->books)) {
        echo "No books is Available \n";
        return;
       }

       foreach($this->books as $book){      //looping through all books
         $book->displaybook();
       }
    }

    private function Findbook($bookid)      //find a book
    {
        Foreach($this->books as $book){
            if($book->getBookID() == $bookid){
                    return $book;
            }
        }
        return null;        //no book was found
    }

    private function Findmember($memberid)            //find a member
    {
        foreach($this->members as $member){
            if($member->getMemeberid()==$memberid){
                return $memberid;
            }
        }
        return null;            //no member was found
    }

    public function issuebook($bookid,$memberid)            //issue books
    {
        $books= $this->Findbook($bookid);
        $members= $this->Findmember($memberid);
        
        if($books == null){
                echo "Book not found";
                return;
        }

        if($members == null){
                echo "Member not found";
                return;
        }

        if(!$book->isavailable()){
            echo "Book is already issued";
            return;
        }
        $book->issuebook();

        echo "Book issued successfully\n";
        echo "Book:".$book->getBookName(). "\n";
        echo " Member:".$member->getMemebername(). "\n";


    }

    public function returnbook($bookid){
        $book= $this->Findbook($bookid);

        if ($book == null){
            echo "Book not fount\n";
            return;

        }
        if($book == isavailable())
            {
                    echo "this book is not currently issued\n";
                    return;

            }
        $book->returnbook();
        echo "book retured successfully\n";
    }
     
    public function displaymember()
    {
        if(empty($this->members)){
            echo "no members are registerd \n";
            return;
        }
        foreach($this->members as $member ){
                $member->displaymember();
        }
    }


}
?>