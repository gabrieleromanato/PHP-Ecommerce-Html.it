<?php
namespace PHPEcommerce;
require_once(__DIR__  . '/core/config.php');
require_once(__DIR__ . '/core/autoload.php');
$shop = new Shop();
$app = new Dispatcher($shop);
$app->handle();
?>
