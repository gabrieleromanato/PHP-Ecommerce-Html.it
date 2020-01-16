<?php 
require_once('config.php');
require_once('classes/autoload.php');
session_start();
$shop = new Shop();
$shop->addToCart();
?>