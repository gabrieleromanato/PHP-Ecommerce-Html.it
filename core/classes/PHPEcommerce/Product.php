<?php

namespace PHPEcommerce;

class Product {
    public $id;
    public $name;
    public $description;
    public $price;
    public $manufacturer;
    public $image;
    public $slug;

    public function __construct($id, $name, $description, $price, $manufacturer, $image, $slug) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->manufacturer = $manufacturer;
        $this->image = $image;
        $this->slug = $slug;
    }
}