<?php
require_once("Task.php");

class ToDoList
{
    private $tasks=[];
    private $nextId =1;

    public function addTask( $Title)
    {
        $task=new Task($this->nextId,$Title);
        $this->tasks[]=$task;
        $this->nextId++;

        echo "\nTask added successfully\n";
        
    }

    public function displaytask()
    {
            if(empty($this->tasks))
                {
                    echo "\nNo tasks are available\n";
                    return ;
                }
            foreach($this->tasks as $task)
                {
                        echo "ID: " .$task->getId(). "\n";
                        echo "Title: " .$task->getTitle(). "\n";
                        echo "Status: " .$task->getStatus(). "\n";
                }
    }

    public function Searchtask($keyword)
    {
        $found = false;
         foreach($this->tasks as $task)
            {
                if(stripos($task->getTitle(),$keyword) !== false)               //checks whether the task exist inside the task title, stripos return the position where the  text found
                    {
                                echo "\n";
                                echo "ID: ". $task->getId()."\n";
                                echo "Title: ".$task->getTitle(). "\n";
                                echo "Status: ".$task->getStatus(). "\n";

                                $found = true;

                    }

                    
            }
            if(!$found)
                        {
                            echo "\n Task not found";
                        }
    }

    public function Completetask($Id)
    {
        foreach($this->tasks as $task)
            {
            if($task->getId()==$Id)
                {
                    $task->MarkasCompleted();
                    echo "task Completed Successfully\n";
                    return;
                }
            }
            echo "task not found\n";
    }
    public function updateTask($Id, $newTitle)
    {
        foreach($this->tasks as $task)
            {
            if($task->getId()==$Id)
                {
                    $task->updateTitle($newTitle);
                    echo "Task updated Successfully\n";
                    return;
                }
            }
        echo "Task failed to update\n";
    }
    public function Deletetask($Id)
    {
                foreach($this->tasks as $index=>$tasks)
                    {
                            unset($this->tasks[$index]);
                            $this->tasks=array_values($this->tasks);
                    }
    }

}
?>