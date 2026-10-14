<?php
class Puzzle
{
    private $originalsquare;            //correct magic square
    private $puzzle;                    //square with missing values
    private $missingposition;           //missing position where player enters numbers


    public function __construct($square)            
    {
        $this->originalsquare=$square;              //assgning  originalsquare  to square
        $this->puzzle=$square;                      //assigning  puzzle to square
        $this->missingposition=[];                  //assign missing points to list
    }

    public function Createpuzzle()
    {
            $this->missingposition=[
                [0,1],
                [1,0],
                [1,2],
                [2,1]
            ];

        foreach($this->missingposition as $position){
            $row=$position[0];
            $column=$position[1];

            $this->puzzle[$row][$column]=null;
        }
    }



    public function Displaypuzzle()
    {
            for($i=0; $i<count($this->puzzle); $i++){
                for($j=0; $j<count($this->puzzle[$i]); $j++)
                    {
                        if($this->puzzle[$i][$j]===null)
                            {
                                echo "_";
                            }else{
                                    echo $this->puzzle[$i][$j]. " ";
                            }
                    }
                    echo "\n";
            }
    }
    public function getpuzzle()                     //getting the puzzle
    {
        return $this->puzzle;
    }


    public function getOriginalSquare()
    {
        return $this->originalsquare;
    }


    public function  getmissingposition()           //getting the missing position
    {
        return $this->missingposition;
    }


    public function fillnumber($row, $column, $number)       
    {
        //if($this->ismissingpostion($row,$column))
           // {    
                $this->puzzle[$row][$column]=$number; 
               // return true; 
            //}
            //return false;                 //assign the number to give row column
    }
}
?>