<?php

namespace PHPEcommerce;

class Mail {
    public static function sendConfirmation($order_data, $customer_data) {
        $cart_items = unserialize($order_data['cart']);
        $template = file_get_contents(ABSPATH . 'core/src/templates/email/order-confirm.txt');
        $cart = '';
        $total = money_format('%.2n', floatval($order_data['total']));
        $total_taxes = money_format('%.2n', floatval($order_data['total_taxes']));

        foreach($cart_items as $item) {
            $cart .= ucwords($item['name']) . "\n";
            $cart .= str_repeat('=', strlen($item['name'])) . "\n";
            $cart .= money_format('%.2n',floatval($item['price'])) . ' / ' . $item['quantity'] . "\n";
            $cart .= str_repeat('=', strlen($item['name'])) . "\n";
        }

        $body = str_replace([
            '{fullname}',
            '{order_id}',
            '{cart}',
            '{total}',
            '{total_taxes}',
            '{billing}',
            '{shipping}'
        ], [
            $customer_data['fullname'],
            $order_data['id'],
            $cart,
            $total,
            $total_taxes,
            $order_data['billing'],
            $order_data['shipping']
        ], $template);

        $headers = "From: " . EMAIL_FROM . "\r\n";
        $to = $customer_data['email'];
        $subject = 'PHPEcommerceMVC: Order ' . $order_data['id'];

        return mail($to, $subject, $body, $headers);
    }
}