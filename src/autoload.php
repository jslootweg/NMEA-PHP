<?php

spl_autoload_register(function ($class_name)
{
    $file = dirname(__FILE__) . "/" . $class_name . '.php';
    
    if (is_readable($file))
    {
        require_once $class_name . '.php';
    }
});
