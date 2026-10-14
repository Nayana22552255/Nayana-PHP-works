<?php

class Stack
{
    private $stack=[];          //store the srack elements
    private $top=-1;               //top element of stack
    private $size;                  //size of the stack


    public function __construct($size)
    {
        $this->size=$size;
    }

    public function push($value)
    {
            if($this->isFull())
                {
                    echo "Stack overflow! cannot push values \n";
                    return;
                }  

            $this->top++;
            $this->stack[$this->top]=$value;
    }

    public function pop()
    {
        if($this->isEmpty())
            {
                echo "stack underflow! cannot pop values \n";
                return;
            }
            $value=$this->stack[$this->top];
            unset($this->stack[$this->top]);
            $this->top--;
            
    }

    public function peek()
    {
        $this->stack[$this->top];
        echo "top element is  ". $this->stack[$this->top];
    }

    public function isFull()
    {
        return $this->top == $this->size - 1;
    }

    public function IsEmpty()
    {
        return $this->top == -1;
    }

    public function display()
    {
        if($this->isEmpty())
            {
                echo "Stcak is empty\n";
                return;
            }
        for($i= $this->top; $i>=0; $i--)
            {
                echo $this->stack[$i]. "\n";
            }
    }
}

$stack= new Stack(5);

while(true)
{
echo "\n";
echo "1.push element\n";
echo "2.peekt\n";
echo "3.IsFull\n";
echo "4.IsEmpty\n";
echo "5.Pop\n";
echo "6.Display\n";
echo "7.exit\n";
echo "\n";

$choice=readline("enter your choice");

switch($choice)
{
case "1":
        {
            $value=readline("enter the value");
            $stack->push($value);
            break;
        
        }
case "2":
        {
            $stack->peek();
            break;
        }
case "3":
        {
            if($stack->isFull())
            {
                     echo "stack is full\n";
            }else{
                        echo "stack is not full\n";

            }
            break;
                   

        }
case "4":
            {
                    if($stack->IsEmpty())
                        {
                            echo "stack is empty\n";

                        } else{
                            echo "stack is not empty\n";
                        }
                    break;
            }
case "5":
            {
                    $stack->pop();
                    echo "$value is poped out\n";
                    break;
            }
case "6":
            {
                echo "the stack elements are\n";
                $stack->display();
                break;
            
            }
case "7":
            {
                exit;
            }

}
}

?>