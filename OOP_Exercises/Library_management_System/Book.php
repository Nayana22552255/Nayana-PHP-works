<?php
class Book
{
    private $bookid;
    private $bookname;
    private $authorname;
    private $isavailable;

    public function __construct($bookid,$bookname,$authorname)
    {
        $this->bookid = $bookid;
        $this->bookname = $bookname;
        $this->authorname = $authorname;
        $this->isavailable = true;

    }


    public function getBookID()
    {
        return $this->bookid;
    }
    public function getBookName()
    {
        return $this->bookname;
    }
     public function getAuthorname()
    {
        return $this->authorname;
    }
    public function getIsavailable()
    {
        return $this->isavailable;
    }
    public function issuebook()
    {
        return $this->isavailable=false;
    }
    public function returnbook()
    {
         return $this->isavailable=true;
    }


    public function displaybook()
    {
    echo "\n";    
    echo "BookId: ".$this->bookid."\n";
        echo "Title: ".$this->bookname."\n";
        echo "Author: ".$this->authorname."\n";

            if($this->isavailable){
                    echo "Status: Available\n";
            }else{
                    echo "Status: Issued\n";
            }
            
    }

    public function toarray()           //convert objects into array
    {
        return [
        "bookId"=>$this->bookid,
        "title"=>$this->bookname,
        "author"=>$this->authorname,
        "isavailable"=>$this->isavailable ];
    }


}
?>