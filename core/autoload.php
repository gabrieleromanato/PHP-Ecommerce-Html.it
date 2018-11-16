<?php

spl_autoload_register(function($className) {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $path = (strpos($className, 'PHPEcommerce') !== false) ? ABSPATH . 'core/classes/' . $className . '.php' : ABSPATH . 'core/classes/PHPEcommerce/' . $className . '.php';
    require_once($path);
});


