<?php

namespace PHPEcommerce;

class Session {
    public static function start() {
        session_start();
    }
    public static function setItem($key, $value, $serialize = false) {
        if(!$serialize) {
            $_SESSION[$key] = $value;
        } else {
            $_SESSION[$key] = serialize($value);
        }
    }

    public static function getItem($key, $unserialize = false) {
        if(!$unserialize) {
            return $_SESSION[$key];
        } else {
            return unserialize($_SESSION[$key]);
        }
    }

    public static function hasItem($key) {
        return (isset($_SESSION[$key]));
    }

    public static function end() {
        session_destroy();
        $_SESSION = [];
    }
}