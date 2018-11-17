<?php

namespace PHPEcommerce;

class Ajax {
    protected $db;
    protected $endpoints = ['infinite', 'autocomplete'];

    public function __construct() {
        $this->db = new Database();
    }

    public function isEndpoint($value) {
        return (in_array($value, $this->endpoints));
    }

    public function autocomplete() {
        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            $s = $this->db->escape(trim($_GET['s']));
            $products = Templater::autocomplete($this->db->select("SELECT * FROM products WHERE title LIKE '%$s%'  ORDER BY price ASC LIMIT 10"));
            $this->json($products);
        } else {
            $this->invalidRequest();
        }
    }

    public function infinite() {
        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            $total_products = $this->db->select("SELECT count(*) AS total FROM products WHERE price > 0");
            $per_page = 10;
            $pages = floor(intval($total_products[0]['total'] ) / $per_page);
            $current_page = (isset($_GET['page']) && ctype_digit($_GET['page']) && intval($_GET['page']) <= $pages) ? intval($_GET['page']) : 1;
            $offset = $current_page * $per_page;
            $products = Templater::products($this->db->select("SELECT * FROM products ORDER BY price ASC LIMIT $per_page OFFSET $offset"));
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