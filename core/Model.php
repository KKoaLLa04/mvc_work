<?php

abstract class Model extends Database
{
    protected $db;
    public function __construct()
    {
        parent::__construct();
        $this->db = new Database();
    }

    abstract function tableFill();

    abstract function selectFill();

    abstract function primaryKey();

    public function all()
    {
        $tableFill = $this->tableFill();
        $selectFill = $this->selectFill();

        if (empty($selectFill)) {
            $selectFill = '*';
        }

        $sql = "SELECT $selectFill FROM $tableFill";
        $query = $this->query($sql);
        if (!empty($query)) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    public function find($value)
    {
        $tableFill = $this->tableFill();
        $selectFill = $this->selectFill();
        $primaryKey = $this->primaryKey();

        if (empty($selectFill)) {
            $selectFill = '*';
        }

        $sql = "SELECT $selectFill FROM $tableFill WHERE $primaryKey='$value'";
        $query = $this->query($sql);
        if (!empty($query)) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
        }

        return $data;
    }
}
