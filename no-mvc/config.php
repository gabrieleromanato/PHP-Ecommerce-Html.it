<?php

define( 'VAT_VALUE', 22 );
define('SITE_URL', '');
define('DB_USER', '');
define('DB_NAME', '');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('ABSPATH', $_SERVER['DOCUMENT_ROOT'] . '/');
define('PP_BUSINESS', '');
define('PP_CURRENCY', 'EUR');
define('PP_LOCATION', 'IT');
define('PP_RETURN_URL', SITE_URL . 'thank-you');
define('PP_RETURN_TXT', 'Return to PHPEcommerce');
define('PP_CANCEL_URL', SITE_URL . 'cancel');
define('PP_SHIPPING', 0);
define('PP_FORM_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
define('EMAIL_FROM', 'PHPEcommerce <orders@' . $_SERVER['HTTP_HOST'] . '>');