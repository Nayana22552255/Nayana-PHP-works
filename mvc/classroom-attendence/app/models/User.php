<?php

class User extends Model 
{
    public function findByUsername($username)
    {
        $sql="SELECT * FROM users WHERE username=:username";
        $stmt=$this->db->prepare($sql);
        $stmt->execute([
            ':username'=>$username
        ]);
        return $stmt->fetch();
    }
}
?>