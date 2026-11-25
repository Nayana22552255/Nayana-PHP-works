
<?php

abstract class Model
{
    protected $db;

    // Constructor
    public function __construct()
    {
        $this->db = Database::connect();
    }

}
