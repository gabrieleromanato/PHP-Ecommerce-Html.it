<?php

namespace PHPEcommerce;

class Dispatcher {
    protected $params;
    protected $controller;

    public function __construct($controller) {
        $this->controller = $controller;
        $this->params = $this->parse($_SERVER['REQUEST_URI']);
    }

    public function handle() {
        $action = $this->params['method'];
        $args = $this->params['args'];
        if(method_exists($this->controller, $action)) {
            if(!is_null($args)) {

                $this->controller->$action($args);
            } else {
                $this->controller->$action();
            }
        } else {
            Site::error();
        }
    }

    protected function parse($path) {
        if($path == '/') {
            return [
                'method' => 'index',
                'args' => null
            ];
        } else {
            $parts = array_values(array_filter(explode('/', $this->removeQueryStringVariables($path))));
            $method = $this->convertToCamelCase($parts[0]);
            $args = array_slice($parts, 1);
            return [
                'method' => $method,
                'args' => (count($args) > 0 ) ? $args : null
            ];
        }
    }

    protected function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    protected function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    protected function removeQueryStringVariables($url) {
        $parts = explode('?', $url);
        return $parts[0];
    }
}