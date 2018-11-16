<?php

namespace PHPEcommerce;

abstract class Tax {
    abstract protected function add($amount);
}