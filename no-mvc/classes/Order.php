<?php

class Order {
    private $id;
    private $cart;
    private $customer;
    private $billing;
    private $shipping;
    private $total;
    private $totalWithTaxes;
    private $status;

    public function __construct($id, Cart $cart, Customer $customer) {
        $this->id = strtoupper($id);
        $this->cart = $cart;
        $this->customer = $customer;
        $this->total = $this->cart->getTotal();
        $this->status = 0;
        $this->setTotalWithTaxes();
    }

    private function setTotalWithTaxes() {
        if(class_exists('Vat') && defined('VAT_VALUE')) {
            $vat = new Vat(VAT_VALUE);
            $this->totalWithTaxes = $vat->add($this->total);
        } else {
            $this->totalWithTaxes = $this->total;
        }
    }

    public function getStatus() {
        return $this->status;
    }

    public function getCart() {
        return $this->cart;
    }

    public function getCustomer() {
        return $this->customer;
    }

    public function getId() {
        return $this->id;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setShipping($shipping) {
        $this->shipping = $shipping;
    }
    public function getShipping() {
        return $this->shipping;
    }

    public function setBilling($billing) {
        $this->billing = $billing;
    }

    public function getBilling() {
        return $this->billing;
    }

    public function getTotal() {
        return $this->total;
    }

    public function getTotalWithTaxes() {
        return $this->totalWithTaxes;
    }

    public function save() {
        $db = new Database();
        $id = $this->id;
        $created = strftime('%Y-%m-%d %H:%M:%S', time());
        $cart = serialize($this->cart->getItems());
        $customer = $this->customer->getId();
        $billing = $this->billing;
        $shipping = $this->shipping;
        $total = $this->total;
        $total_taxes = $this->totalWithTaxes;
        $status = $this->status;

        $insert = "INSERT INTO orders (id, created, cart, customer, billing, shipping, total, total_taxes, status) VALUES (";
        $insert .= "'$id', '$created', '$cart', '$customer', '$billing', '$shipping', '$total', '$total_taxes', $status)";

        return $db->query($insert);
    }
}