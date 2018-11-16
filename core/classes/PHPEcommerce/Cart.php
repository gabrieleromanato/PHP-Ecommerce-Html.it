<?php

namespace PHPEcommerce;

class Cart {
    private $items;
    private $total;

    public function __construct() {
        $this->items = [];
        $this->total = 0.00;
    }

    public function emptyCart() {
        $this->items = [];
        $this->total = 0.00;
    }

    public function setItems($items) {
        $this->items = $items;
    }

    public function getItems() {
        $items = [];

        if($this->hasItems()) {
            foreach($this->items as $item) {
                $items[] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'manufacturer' => $item['manufacturer'],
                        'image' => $item['image'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal']
                ];
            }
        }    

        return $items;
    }

    public function setTotal($value) {
        $this->total = $value;
    }

    public function getTotal() {
        return $this->total;
    }

    public function updateCart(Product $product, $quantity) {
        if($this->hasItems()) {
            foreach($this->items as &$item)  {
                if($product->id == $item['id']) {
                    $item['quantity'] = $quantity;
                    $item['subtotal'] = ($product->price * $quantity);
                    $this->calculateTotal();
                }
            }
            
        }
    }

    public function removeFromCart(Product $product) {
        if($this->hasItems()) {
            $i = -1;
            foreach($this->items as $item) {
                $i++;
                if($product->id == $item['id']) {
                    unset($this->items[$i]);
                    $this->calculateTotal();
                }
            }
        }
    }

    public function addToCart(Product $product, $quantity) {
        if($quantity < 1) {
            return;
        }
        $item = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'manufacturer' => $product->manufacturer,
            'image' => $product->image,
            'quantity' => $quantity, 
            'subtotal' => ($product->price * $quantity)
        ];
        $this->items[] = $item;
        $this->calculateTotal();
    }

    private function isInCart(Product $product) {
        if( $this->hasItems()) {
           foreach( $this->items as $item ) {
               if($item['id'] == $product->id) {
                   return true;
               }
           }
           return false;
        } else {
            return false;
        }
    }

    private function calculateTotal() {
        $this->total = 0.00;
        if($this->hasItems()) {
            $tot = 0.00;
            foreach($this->items as $item) {
                $tot += $item['subtotal'];
            }
            $this->total = $tot;
        }
    }

    private function hasItems() {
        return ( count( $this->items ) > 0 );
    }
}