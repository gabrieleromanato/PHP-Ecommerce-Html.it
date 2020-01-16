<?php

$classes = [
    'Database.php', 'Product.php', 'Cart.php', 'Validator.php', 'Templater.php',
    'Router.php', 'Tax.php', 'Vat.php', 'Customer.php', 'Order.php', 'PayPal.php', 'Mail.php', 'Shop.php'
];
foreach($classes as $class) {
    require_once(ABSPATH . 'classes/' . $class);
}