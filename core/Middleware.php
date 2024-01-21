<?php

abstract class Middleware
{
    public $db;
    abstract public function handle();
}
