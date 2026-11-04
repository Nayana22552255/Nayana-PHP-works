<?php
class Task
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn=$conn;
    }

    public function addTask(string $title, string $description,string $priority,?string $due_date): bool 
    {
        $sql="INSERT INTO task(title,description,priority,due_date)
                VALUES(:title,:description,:priority,:due_date)";

        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([':title'=>$title,
                                ':description'=>$description,
                                ':priority'=>$priority,
                                ':due_date'=>$due_date]);
    

    }

    public function getTask(): array
    {
        $sql="SELECT * FROM task ORDER BY task_id DESC";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTaskById(int $id): ?array
    {
        $sql="SELECT * FROM task WHERE task_id=:id";
        $stmt=$this->conn->prepare($sql);
        $stmt->execute([':id'=>$id]);
        $task=$stmt->fetch(PDO::FETCH_ASSOC);
        return $task === false ? null : $task;
    }

    public function updateTask(int $id,string $title, string $description,string $priority, ?string $due_date): bool 
    {
        $sql="UPDATE task SET title=:title,
                description=:description,
                priority=:priority,
                due_date=:due_date WHERE task_id=:id";

        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([
                                ':title'=>$title,
                                ':description'=>$description,
                                ':priority'=>$priority,
                                'due_date'=>$due_date,
                                ':id'=>$id
        ]);


    }
    public function completeTask(int $id):bool 
    {
        $sql="UPDATE task SET status='Completed'  WHERE task_id=:id";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([':id'=>$id]);
    }

    public function deleteTask(int $id): bool 
    {
        $sql="DELETE FROM task WHERE task_id=:id";
        $stmt=$this->conn->prepare($sql);
        return $stmt->execute([':id'=>$id]);
    }
}