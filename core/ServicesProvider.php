<?php

abstract class ServicesProvider
{
    public $db;

    abstract public function boot();
}
