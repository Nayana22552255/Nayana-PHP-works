<?php
class Poll
{
    private $questions;
    private $options;
    private $votes=[];

    public function __construct($questions,$options)
    {
        $this->questions=$questions;
        $this->options=$options;

        foreach($options as $index=> $option)
            {
                    $this->votes[$index]=0;
            }

        
    }

    public function displaypoll()
    {
        echo "\n". $this->questions. "\n";


        foreach($this->options  as  $index => $option)
            {
                echo $index. ".". $option. "\n";
            }

            echo "0. Exit and show results\n";
    }

    public function vote($choice)
    {
        if(isset($this->options[$choice]))
            {
                $this->votes[$choice]++;
                echo "vote recorded successfully!\n";
            }
            else
            {
                echo "Invalid choice please try again \n";
            }
    }

    public function showResults()
    {
        echo "\n===================POLL RESULITS==============\n";
        $totalvotes= array_sum($this->votes);

        foreach($this->options as $index=> $option)
            {
                $count=$this->votes[$index];
                echo "$option: ".$count. "votes"."\n";


             if($totalvotes > 0)
                {
                    $percentage=($count / $totalvotes) *100;
                    echo "(". round($percentage, 2). "%)";
                    
                }
                 echo "\n";
            }
           
            echo "Total votes:". $totalvotes. "\n";
    }
}

$options=[
    1=>"PHP",
    2=>"PYTHON",
    3=>"JAVA",
    4=>"JAVASCRIPT" 
];

$poll= new Poll(
    "which programming language do you like most",$options);

do{
    $poll->displaypoll();
    $choice=(int) readline("enter your choice: ");


    if($choice == 0)
        {
            break;
        }
    $poll->vote($choice);
}while(true);

$poll->showResults();
?>