<?php

class Router {
    public static function status($code = '404', $msg = 'Not Found') {
        if(!headers_sent()) {
            header('HTTP/1.1 ' . $code . ' ' . $msg);
        }
    }

    public static function mime( $mime ) {
        if(!headers_sent()) {
            header( 'Content-Type: ' . $mime );
        }
    }

    public static function redirect($url) {
        if(!headers_sent()) {
            header('Location: ' . $url);
            exit;
        }
    }
}