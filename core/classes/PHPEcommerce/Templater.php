<?php

namespace PHPEcommerce;

class Templater {
    public static function manufacturers($data) {
        $output = [];
        foreach($data as $d) {
            $output[] = [
                'name' => ucwords($d['manufacturer']),
                'link' => SITE_URL . 'manufacturer/' . $d['manufacturer']
            ];
        }
        return $output;
    }
    public static function products($products = []) {
        $output = [];
        if(count($products) > 0) {
            foreach($products as $product) {
                $display = [
                    'id' => $product['id'],
                    'name' => ucwords($product['title']),
                    'description' => ucfirst($product['description']),
                    'manufacturer' => ucfirst($product['manufacturer']),
                    'price' => str_ireplace('eu', '&euro;', money_format('%.2n', floatval($product['price']))),
                    'raw_price' => number_format(floatval($product['price']), 2, '.', ''),
                    'image' => SITE_URL . 'public/assets/images/' . $product['image'],
                    'link' => SITE_URL . 'product/' . $product['slug'],
                    'add_to_cart_link' => SITE_URL . 'add-to-cart/?id=' . $product['id'] . '&amp;quantity=1'
                ];
                $output[] = $display;
            }
        }
        return $output;
    }

    public static function frontCart() {
        $output = [];
        if(isset($_SESSION['cart'])) {
            $session_cart = unserialize($_SESSION['cart']);
            $items = self::cart($session_cart['items']);
            if(count($items) > 0) {
                $total = self::total($session_cart['total']);
                $output['cart'] = ['items' => $items, 'total' => $total];
            }
        }
        return $output;
    }

    public static function cart($items = []) {
        $output = [];
        if(count($items) > 0) {
            foreach($items as $item) {
                $out = [
                    'id' => $item['id'],
                    'name' => ucwords($item['name']),
                    'price' => str_ireplace('eu', '&euro;', money_format('%.2n',floatval($item['price']))),
                    'quantity' => $item['quantity'],
                    'subtotal' => str_ireplace('eu', '&euro;', money_format('%.2n',floatval($item['subtotal']))),
                    'link' => SITE_URL . 'product/' . $item['slug'],
                    'image' => SITE_URL . 'public/assets/images/' . $item['image']
                ];

                $output[] = $out;
            }
        }
        return $output;
    }

    public static function total($value) {
        return str_ireplace('eu', '&euro;', money_format('%.2n', floatval($value)));
    }

    public static function rawTotal($value) {
        return number_format(floatval($value), 2, '.', '');
    }
}