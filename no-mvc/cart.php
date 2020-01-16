<?php 
require_once('config.php');
require_once('classes/autoload.php');
session_start();
setlocale(LC_ALL, 'it_IT');
$shop = new Shop();
$shop->cart();
?>