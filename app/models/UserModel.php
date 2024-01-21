<?php
// UserModel Model

class UserModel extends Model
{

    public function tableFill()
    {
        return 'table_here';
    }

    public function selectFill()
    {
        return '';
    }

    public function primaryKey()
    {
        return 'id';
    }
}