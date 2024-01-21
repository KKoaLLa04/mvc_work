<?php

$sessionKey = Session::isInvalid();
$errors = Session::flash($sessionKey . '_errors');
$old = Session::flash($sessionKey . '_old');

if (!function_exists('form_error')) {
    function form_error($fieldName)
    {
        global $errors;

        if (!empty($errors) && !empty($errors[$fieldName])) {
            return $errors[$fieldName];
        }

        return false;
    }
}

if (!function_exists('old')) {
    function old($fieldName, $default = '')
    {
        global $old;
        if (!empty($old) && !empty($old[$fieldName])) {
            return $old[$fieldName];
        }

        return $default;
    }
}
