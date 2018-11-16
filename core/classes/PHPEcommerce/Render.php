<?php

namespace PHPEcommerce;

use PHPEcommerce\Session as Session;

class Render {
    public static function getDeviceClass() {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $class = (stristr($ua, 'mobile') !== false ) ? 'mobile' : 'desktop';
        return $class;
    }
    public static function getLocale($var = '') {
        $path = ABSPATH . 'core/src/locales/';
        if(empty($var)) {
            return $path . LOCALE . '.php';
        } else {
            $lang = strtolower($var) . '_' . strtoupper($var) . '.php';
            if(file_exists( $path . $lang )) {
                return $path . $lang;
            } else {
                return $path . LOCALE . '.php';
            }
        }
    }
    public static function view($template, $vars) {
        $lang_src = (Session::hasItem('lang')) ? self::getLocale(Session::getItem('lang')) : self::getLocale();

        extract($vars);
        $template_path = ABSPATH . 'views/' . $template . '.php';
        $deviceClass = self::getDeviceClass();
        if(file_exists($template_path)) {
            include($lang_src);
            include($template_path);
        }
    }
}