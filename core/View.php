<?php

class View
{
    public static $dataShare = [];

    public static function share($data)
    {
        return self::$dataShare = $data;
    }
}
