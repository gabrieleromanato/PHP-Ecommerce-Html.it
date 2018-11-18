<?php

namespace PHPEcommerce;

class Validator {
    public static function sanitize( $value ) {
        require_once ABSPATH . 'core/lib/htmlpurifier/library/HTMLPurifier.auto.php';
        $config = \HTMLPurifier_Config::createDefault();
        $purifier = new \HTMLPurifier($config);
        $clean_html = $purifier->purify($value);
        return $clean_html;
    }
    public static function isPayPalRequest() {
        return (isset($_REQUEST['st']) && strtolower($_REQUEST['st']) == 'completed');
    }
    public static function isProductId($id) {
        return (filter_var(intval($id), FILTER_VALIDATE_INT));
    }

    public static function isProductSlug($value) {
        return (preg_match('/^[a-z-0-9-]+$/', $value));
    }

    public static function isCartAddRequest($id, $qty) {
        return (filter_var(intval($id), FILTER_VALIDATE_INT) && filter_var(intval($qty), FILTER_VALIDATE_INT));
    }

    public static function isOrderRequest($form_data) {
        $output = [];
        $has_shipping = (isset($form_data['shipping']));
        $billing_errs = [];
        $shipping_errs = [];
        $is_valid = true;

        foreach($form_data['billing'] as $field => $value) {
            if($field == 'email') {
                if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $billing_errs['billing_email'] = 'Invalid e-mail address';
                }
            } else {
                if(empty($value)) {
                    $billing_errs['billing_' . $field] = 'Required field';
                }
            }
        }

        if(count($billing_errs) > 0) {
            $is_valid = false;
        }

        if($has_shipping) {
            foreach($form_data['shipping'] as $f => $v) {
                if($f == 'email') {
                    if(!filter_var($v, FILTER_VALIDATE_EMAIL)) {
                        $shipping_errs['shipping_email'] = 'Invalid e-mail address';
                    }
                } else {
                    if(empty($v)) {
                        $shipping_errs['shipping_' . $f] = 'Required field';
                    }
                }
            }

            if(count($shipping_errs) > 0) {
                $is_valid = false;
            }
        }

        $output['status'] = $is_valid;
        if(count($billing_errs) > 0) {
            $output['billing'] = $billing_errs;
        }
        if(count($shipping_errs) > 0) {
            $output['shipping'] = $shipping_errs;
        }
        if($is_valid) {
            $output['redirect'] = SITE_URL . 'payment/';
        }

        return $output;
    }
}