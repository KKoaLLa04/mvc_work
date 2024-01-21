<?php

class DB
{
    public $db;
    public function __construct()
    {
        $this->db = new Database();
    }
}
