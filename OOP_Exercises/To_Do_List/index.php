<?php
require_once("ToDoList.php");

$todo= new ToDoList();

while(True){

echo "\n======== TO-DO LIST=========\n";
echo "1.Add Task \n";
echo "2.Display Task\n";
echo "3.Search Task\n";
echo "4.Complete Task\n";
echo "5.Update Task\n";
echo "6.Delete Task\n";
echo "7.Exit\n";

$choice =readline("enter your choice");

switch($choice)
{
    case 1:
        $Title=readline("enter the Task Title:  ");
        echo  $todo->addTask($Title). "\n";
        break;

    case 2:
       
        $todo->displaytask();
        echo "\n";
        break;

    case 3:
        $keyword=readline("please enter the keyword you want to search:  ");
        echo $todo->Searchtask($keyword). "\n";
        break;

    case 4: 
        $Id=readline("enter the Task ID:  ");
        echo $todo->Completetask($Id). "\n";
        break;

    case 5:
        $Id=readline("enter the Task ID which you want to update the task:  ");
        $newTitle=readline("enter the new Title \n");
        echo "Update Task: ".$todo->updateTask($Id,$newTitle). "\n";
        break;

    case 6:
        $Id=readline("enter the Task ID which you want to delete:  ");
        echo $todo->Deletetask($Id). "\n";
        break;

    case 7:
         echo "Today task are done! \n";
         exit;
    
    default:
            echo "plaese choose the right choices from the above \n";
            break;
}
}

?>