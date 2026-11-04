<?php
require_once 'Database.php';
require_once 'Task.php';


try
{
$database=new Database();
$conn=$database->connect();
$task=new Task($conn);

while(true)
    {
        echo "\n-----------------------------\n";
        echo "============TO-DO-LIST=============\n";
        echo "1. Add a new Task\n";
        echo "2. View All Task\n";
        echo "3. View task by ID\n";
        echo "4. Update an Existing Task\n";
        echo "5. Delete a Task\n";
        echo "6. Mark as task Completed\n";;
        echo "0. Exit\n";
        echo "--------------------------------\n";

        $choice=(int)readline("Enter a Choice: ");

        switch($choice)
        {
            case 1:
                        $title=readline("Enter the Task Title: ");
                        $description=readline("Enter the Description: ");
                        $priority=readline("Enter the Priority Task: ");
                        $due_date=readline("Enter the due date: ");
                        $task->addTask($title,$description,$priority,$due_date);
                        echo "Task added Successfully\n";
                        break;

            case 2:
                        $tasks=$task->getTask();
                        if(empty($tasks))
                        {
                            echo "No tasks found.\n";
                            break;
                        }

                        foreach($tasks as $row)
                        {
                            echo "ID: {$row['task_id']}\n";
                            echo "Title: {$row['title']}\n";
                            echo "Description: {$row['description']}\n";
                            echo "Priority: {$row['priority']}\n";
                            echo "Due date: ".($row['due_date'] ?? 'None')."\n";
                            echo "Status: {$row['status']}\n";
                            echo "-----------------------------\n";
                        }
                        break;

            case 3:
                        $id=(int)readline("enter the id of task: ");
                        $row=$task->getTaskById($id);
                        if($row===null)
                        {
                            echo "No task found with ID {$id}.\n";
                            break;
                        }
                        echo "\n--------------------------\n";
                        echo "ID: {$row['task_id']}\n";
                        echo "Title: {$row['title']}\n";
                        echo "Description: {$row['description']}\n";
                        echo "Priority: {$row['priority']}\n";
                        echo "Due date: ".($row['due_date'] ?? 'None')."\n";
                        echo "Status: {$row['status']}\n";
                        
                        break;

            case 4:
                        $id=(int)readline("Enter the task id you wnat to update:  ");
                        $title=readline("Enter the title: ");
                        $description=readline("Enter the description: ");
                        $priority=readline("Enter the priority: ");
                        $due_date=readline("Enter the due date: ");
                        $task->updateTask($id,$title,$description,$priority,$due_date);
                        break;
                    
            case 5:
                        $id=(int)readline("Enter the ID: ");
                        $task->deleteTask($id);
                        echo "Task deleted Successfully\n";
                        break;

                   

            case 6:
                        $id=(int)readline("Enter the Id: ");
                        $task->completeTask($id);
                        break;

            default :
                        echo "Inavlid choice please choose a correct choice\n";

        }
    }


}catch(PDOException $e)
{
    echo "Error in the To-Do-List". $e->getMessage(). "\n";
}