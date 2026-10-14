<?php
class Question
{
    private $num1;
    private $num2;
    private $operator;
    private $correctanswer;

    public function generatequestion()
    {
        $this->num1=rand(0,9);               //generate random nuber between 1 to 100
        $this->num2=rand(0,9);

        $operators=[" + ", " - "," * "];            //declairing the operators

        $this->operator=$operators[array_rand($operators)];         //connecting the operators with array_rand function 
        $this->calculateanswer();                                   //declairing the calculate answer
    }                                                                   

    public function calculateanswer()                                       //for calculating  and store answer
    {
        switch($this->operator)
        {
            case " + ":
                $this->correctanswer=$this->num1 + $this->num2;
                break;
              
            case " - ":
                $this->correctanswer=$this->num1 - $this->num2;
                break;

            case " * ":
                $this->correctanswer=$this->num1 * $this->num2;
                break;
        }
    }
    public function getquestion()                       //for getting the math question
    {
        return $this->num1." ".$this->operator. " ". $this->num2. "\n";
    }
    
    public function getcorrectanswer()                  //for getting the correct answer
    {
        return $this->correctanswer;
    }
}
?>