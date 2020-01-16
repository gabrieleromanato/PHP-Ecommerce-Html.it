<?php

class PayPal {

    protected $settings;
    protected $items;

    public function __construct($options = []) {
        if(count($options) > 0) {
            $this->settings = $options;
        } else {
            $this->settings = [
                'business' => PP_BUSINESS,
                'currency' => PP_CURRENCY,
                'location' => PP_LOCATION,
                'returnurl' => PP_RETURN_URL,
                'returntxt' => PP_RETURN_TXT,
                'cancelurl' => PP_CANCEL_URL,
                'shipping' => PP_SHIPPING,
                'formurl' => PP_FORM_URL
            ];
        }
        $this->items = [];
    }

    public function addItems($cart_items) {
        if(count($cart_items) > 0) {
            foreach($cart_items as $cart_item) {
                $item = [
                    'name' => ucwords($cart_item['name']),
                    'price' => $cart_item['price'],
                    'quantity' => $cart_item['quantity']
                ];

                $this->items[] = $item;
            }
        }
    }

    public function hasItems() { return count($this->items) > 0; }

    public function getItems() { return $this->items; }

    public function getSettings() { return $this->settings; }

    public function getTax($total) {
        $vat = new Vat(VAT_VALUE);
        $total_with_tx = $vat->add(floatval($total));

        return ($total_with_tx - floatval($total));
    }
}