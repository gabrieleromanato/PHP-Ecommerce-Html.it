<?php

namespace PHPEcommerce;

class Site {

    public static function error($code = 404) {
        switch($code) {
            case 404:
                Render::view('404', ['title' => '404: Not Found']);
                break;
            case 500:
                Render::view('500', ['title' => '500: Internal Server Error']);
                break;
            default:
                Render::view('404', ['title' => '404: Not Found']);
                break;
        }
    }
}