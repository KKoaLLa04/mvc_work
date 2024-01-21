<?php

class HtmlHelper
{
    public static function formOpen($method = 'get', $action = '')
    {
        echo '<form action="' . $action . '" method="' . $method . '">';
    }

    public static function formClose()
    {
        echo '</form>';
    }

    public static function input($wraperAfter = '', $wraperBefore = '', $type = 'text', $name, $class = '', $id = '', $placeHolder = '', $value)
    {
        echo $wraperAfter . '<input type="' . $type . '" name="' . $name . '" class="' . $class . '" id="' . $id . '" placeHolder="' . $placeHolder . '" value="' . $value . '">' . $wraperBefore;
    }

    public static function submit($label, $class = '')
    {
        echo '<button type="submit" class="' . $class . '">' . $label . '</button>';
    }
}
