<?php

namespace PHPEcommerce;

class Ajax {
    protected $db;
    protected $endpoints = ['infinite'];

    public function __construct() {
        $this->db = new Database();
    }

    public function isEndpoint($value) {
        return (in_array($value, $this->endpoints));
    }

    public function infinite() {
        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            $offset = $this->db->escape(trim($_GET['offset']));
            $int_off = (int) $offset;
            $per_page = 9;
            $off = ( $int_off > 0 ) ? $int_off * $per_page : 0;
            $products = Templater::products($this->db->select("SELECT * FROM products WHERE price > 0 ORDER BY price ASC LIMIT $per_page OFFSET $off"));
            $this->json($products);
        } else {
            $this->invalidRequest();
        }
    }

    protected function invalidRequest() {
        Router::status('400', 'Bad Request');
        $this->json(['statusCode' => 400]);
    }

    protected function json($data) {
        Router::mime('application/json');
        echo json_encode($data);
        exit;
    }
}