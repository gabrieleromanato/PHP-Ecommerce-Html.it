<?php

namespace PHPEcommerce;

class Mail {
    public static function sendConfirmation($order_data, $customer_data) {
        $cart_items = unserialize($order_data['cart']);
        $file = (HTML_EMAIL) ? 'order-confirm.html' : 'order-confirmr.txt';
        $template = file_get_contents(ABSPATH . 'core/src/templates/email/' . $file);
        $cart = '';
        $total = money_format('%.2n', floatval($order_data['total']));
        $total_taxes = money_format('%.2n', floatval($order_data['total_taxes']));

        foreach($cart_items as $item) {
            if(!HTML_EMAIL) {
                $cart .= ucwords($item['name']) . "\n";
                $cart .= str_repeat('=', strlen($item['name'])) . "\n";
                $cart .= money_format('%.2n', floatval($item['price'])) . ' / ' . $item['quantity'] . "\n";
                $cart .= str_repeat('=', strlen($item['name'])) . "\n";
            } else {

                $cart .= sprintf('<tr><td>%s</td><td>%s</td><td>%s</td></tr>', ucwords($item['name']),
                    money_format('%.2n', floatval($item['price'])), $item['quantity']);
            }
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

        $headers = '';

        if(HTML_EMAIL) {
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type:text/html;charset=UTF-8' . "\r\n";
        }

        $headers .= "From: " . EMAIL_FROM . "\r\n";
        $to = $customer_data['email'];
        $subject = 'PHPEcommerce: Ordine n. ' . $order_data['id'];

        return mail($to, $subject, $body, $headers);
    }
}