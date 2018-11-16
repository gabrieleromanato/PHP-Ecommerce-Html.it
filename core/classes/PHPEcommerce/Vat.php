<?php
namespace PHPEcommerce;
use PHPEcommerce\Tax as Tax;

class Vat extends Tax {
    private $value;

    public function __construct($val) {
        $this->value = ( $val / 100 ) + 1;
    }

    public function add($amount) {
        return ($this->value * $amount);
    }
}
