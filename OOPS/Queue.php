<?php

abstract class Queue
{
    protected $items = [];

    abstract public function enqueue($item);
    abstract public function dequeue();
    abstract public function peek();

    public function isempty()
    {
        return empty($this->items);
    }


    public function display()
    {
        if ($this->isempty()) {
            echo "Queue is empty\n";
            return;
        }

        echo "Queue: ";

        foreach ($this->items as $item) {
            echo $item . " ";
        }

        echo "\n";
    }
}

class Myqueue extends Queue
{
    private $size;

    public function  construct__($size)
    {
       $this->size=$size;
    }
    public function enqueue($item)
    {

        $this->items[] = $item;
        echo "$item is added to queue\n";
    }

    public function dequeue()
    {
        if ($this->isempty()) {
            echo "Queue is empty\n";
            return;
        }

        $removedarrayitem = array_shift($this->items);
        echo "$removedarrayitem item removed from queue\n";
    }

    public function peek()
    {
        if ($this->isempty()) {
            echo "Queue is empty\n";
            return;
        }

        echo "Front element of the Queue is " . $this->items[0] . "\n";
    }
}

$queue = new Myqueue(5);

while (true) {
    echo "1.Enqueue\n";
    echo "2.IsEmpty\n";
    echo "3.Peek\n";
    echo "4.Dequeue\n";
    echo "5.Display\n";
    echo "6.Exit\n";

    $choice = readline("enter your choice: ");

    switch ($choice) {
        case "1":
            $item = readline("enter the queue item: ");
            $queue->enqueue($item);
            break;

        case "2":
            if ($queue->isempty()) {
                echo "Queue is empty\n";
            } else {
                echo "Queue is not empty\n";
            }
            break;

        case "3":
            $queue->peek();
            break;

        case "4":
            $queue->dequeue();
            break;

        case "5":
            $queue->display();
            break;

        case "6":
            exit(0);

        default:
            echo "Invalid choice\n";
            break;
    }
}
?>