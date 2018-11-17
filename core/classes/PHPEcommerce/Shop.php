<?php

namespace PHPEcommerce;

use PHPEcommerce\Cart as Cart;
use PHPEcommerce\Customer as Customer;
use PHPEcommerce\Product as Product;
use PHPEcommerce\Order as Order;
use PHPEcommerce\Session as Session;
use PHPEcommerce\Ajax as Ajax;
use Braintree\Gateway as Gateway;
use Braintree\Transaction as Transaction;

class Shop  {
    public $database;

    public function __construct() {
        $this->database = new Database();
        Session::start();
        setlocale(LC_ALL, LOCALE);
    }



    protected function processCheckout($data) {
        $customer = new Customer(uniqid(), $data['billing']['firstname'], $data['billing']['lastname'], $data['billing']['email']);
        if($customer->save()) {
            $sess_cart = Session::getItem('cart', true);
            $cart = new Cart();
            $cart->setItems($sess_cart['items']);
            $cart->setTotal($sess_cart['total']);

            $order = new Order(uniqid(),$cart, $customer);
            $order->setBilling($data['billing']['address']);
            $shipping = (isset($data['shipping'])) ? $data['shipping']['address'] : $data['billing']['address'];
            $order->setShipping($shipping);

            if($order->save()) {
                Session::setItem('order_id', $order->getId());
            }
        }
    }

    protected function ajaxCheckout() {
        $data = [
            'billing' => [
                'firstname' => trim($_POST['billing_firstname']),
                'lastname' => trim($_POST['billing_lastname']),
                'address' => trim($_POST['billing_address']),
                'email' => trim($_POST['billing_email'])
            ],
            'shipping' => [
                'firstname' => trim($_POST['shipping_firstname']),
                'lastname' => trim($_POST['shipping_lastname']),
                'address' => trim($_POST['shipping_address']),
                'email' => trim($_POST['shipping_email'])
            ]
        ];

        $same_billing = (isset($_POST['same-billing']) && $_POST['same-billing'] == '1');

        $form_data = [];

        if($same_billing) {
            $form_data = ['billing' => $data['billing'] ];
        } else {
            $form_data = $data;
        }

        $validation = Validator::isOrderRequest($form_data);

        if($validation['status'] === true) {
            $this->processCheckout($form_data);
        }

        return $validation;
    }

    protected function ajaxCartUpdate() {
        $qtys = $_POST['qty'];
        $parts = explode(',', $qtys);
        $sess_cart = Session::getItem('cart', true);
        $cart = new Cart();
        $cart->setItems($sess_cart['items']);
        $cart->setTotal($sess_cart['total']);

        foreach($parts as $part) {
            $p = explode('-', $part);
            $id = $p[0];
            $q = (int) $p[1];
            $prod_id = $this->database->escape($id);
            $product = $this->database->select("SELECT * FROM products WHERE id = $prod_id");
            $cart_product = new Product($product[0]['id'], $product[0]['title'], $product[0]['description'], floatval($product[0]['price']), $product[0]['manufacturer'], $product[0]['image'], $product[0]['slug']);

            $cart->updateCart($cart_product, $q);
        }

        $session_cart_upd = ['items' => $cart->getItems(), 'total' => $cart->getTotal()];
        Session::setItem('cart', $session_cart_upd, true);

        $output = ['status' => 'ok'];
        return $output;
    }

    protected function ajaxCartRemove() {
        $id = $_POST['id'];
        $prod_id = $this->database->escape($id);
        $product = $this->database->select("SELECT * FROM products WHERE id = $prod_id");
        $cart_product = new Product($product[0]['id'], $product[0]['title'], $product[0]['description'], floatval($product[0]['price']), $product[0]['manufacturer'], $product[0]['image'], $product[0]['slug']);
        $sess_cart = Session::getItem('cart', true);
        $cart = new Cart();
        $cart->setItems($sess_cart['items']);
        $cart->setTotal($sess_cart['total']);
        $cart->removeFromCart($cart_product);
        $session_cart_upd = ['items' => $cart->getItems(), 'total' => $cart->getTotal()];
        Session::setItem('cart', $session_cart_upd, true);
        
        $output = ['status' => 'ok'];
        return $output;
    }

    public function index() {
        $products = Templater::products($this->database->select("SELECT * FROM products ORDER BY price ASC LIMIT 10"));
        $front_cart = Templater::frontCart();

        Render::view('home', ['products' => $products, 'front_cart' => $front_cart]);
    }

    public function search() {
        $query = strtolower(trim(rawurldecode(urldecode($_GET['s']))));
        $s = $this->database->escape($query);
        $total_products = $this->database->select("SELECT count(*) AS total FROM products WHERE title LIKE '%$s%'");
        $front_cart = Templater::frontCart();
        $per_page = 10;
        $pages = floor(intval($total_products[0]['total'] ) / $per_page);
        $current_page = (isset($_GET['page']) && ctype_digit($_GET['page']) && intval($_GET['page']) <= $pages) ? intval($_GET['page']) : 1;
        $offset = ( $total_products > 1 ) ? $current_page * $per_page : 0;
        $products = Templater::products($this->database->select("SELECT * FROM products WHERE title LIKE '%$s%' ORDER BY price ASC LIMIT 10 OFFSET $offset"));

        Render::view('search', ['s' => htmlentities($s), 'pages' => $pages, 'current_page' => $current_page, 'products' => $products, 'front_cart' => $front_cart]);
    }

    public function ajax($args) {
        if(!$args) {
            Router::status('403', 'Forbidden');
            exit;
        } else {
            $endpoint = $args[0];
            $ajax = new Ajax();
            if($ajax->isEndpoint($endpoint) && method_exists($ajax, $endpoint)) {
                $ajax->$endpoint();
            } else {
                Router::status('403', 'Forbidden');
                exit;
            }
        }
    }

    public function lang($args) {
        $allowed = ['it', 'en'];
        if(in_array($args[0], $allowed)) {
            Session::setItem('lang', $args[0]);
            Router::redirect($_SERVER['HTTP_REFERER']);
        } else {
            Router::redirect(SITE_URL);
        }
    }

    public function product($args) {
        if(!Validator::isProductSlug($args[0])) {
            Router::redirect(SITE_URL);
        } else {
            $prod_slug= $this->database->escape($args[0]);
            if($this->database->exists("SELECT * FROM products WHERE slug = '$prod_slug'")) {
                $product = $this->database->select("SELECT * FROM products WHERE slug = '$prod_slug'");
                $displayProduct = Templater::products($product);
                $singleProduct = $displayProduct[0];
                $front_cart = Templater::frontCart();
                $title = $singleProduct['name'];
                Render::view('product', ['title' => $title, 'front_cart' => $front_cart, 'singleProduct' => $singleProduct]);
            } else {
                Router::status('404', 'Not Found');
                $title = '404: Not Found';
                Render::view('404', ['title' => $title]);
            }
        }
    }

    public function manufacturers() {
        $manufacturers = Templater::manufacturers($this->database->select( 'SELECT DISTINCT manufacturer FROM products ORDER BY rand() LIMIT 21'));
        $front_cart = Templater::frontCart();
        Render::view('manufacturers', ['front_cart' => $front_cart, 'manufacturers' => $manufacturers]);

    }

    public function manufacturer($args) {
        $man_slug= $this->database->escape(urldecode($args[0]));
        if($this->database->exists("SELECT * FROM products WHERE manufacturer = '$man_slug'")) {
            $products = Templater::products($this->database->select("SELECT * FROM products WHERE manufacturer = '$man_slug'"));
            $front_cart = Templater::frontCart();
            $title = ucwords($man_slug);
            Render::view('manufacturer', ['title' => $title, 'front_cart' => $front_cart, 'products' => $products]);
        } else {
            Router::status('404', 'Not Found');
            $title = '404: Not Found';
            Render::view('404', ['title' => $title]);
        }
    }

    public function addToCart() {

           if(Validator::isCartAddRequest($_REQUEST['id'], $_REQUEST['quantity'])) {

                $id = $this->database->escape($_REQUEST['id']);
                $quantity = $_REQUEST['quantity'];

                if($this->database->exists('SELECT * FROM products WHERE id = ' . $id)) {
                    $product = $this->database->select("SELECT * FROM products WHERE id = $id");
                    $qty = (int) $quantity;
                        $cart_product = new Product($product[0]['id'], $product[0]['title'], $product[0]['description'], floatval($product[0]['price']), $product[0]['manufacturer'], $product[0]['image'], $product[0]['slug']);
                        if(!isset($_SESSION['cart'])) {
                            $cart = new Cart();
                            $cart->addToCart($cart_product, $qty);
                            $session_cart = ['items' => $cart->getItems(), 'total' => $cart->getTotal()];
                            Session::setItem('cart', $session_cart, true);
                        } else {
                            $sess_cart = unserialize($_SESSION['cart']);
                            $cart = new Cart();
                            $cart->setItems($sess_cart['items']);
                            $cart->setTotal($sess_cart['total']);
                            $cart->addToCart($cart_product, $qty);
                            $session_cart_upd = ['items' => $cart->getItems(), 'total' => $cart->getTotal()];
                            Session::setItem('cart', $session_cart_upd, true);
                        }
                        Router::redirect(SITE_URL . 'cart/');
                } else {
                    Router::redirect(SITE_URL);
                }

           } else {
               Router::redirect(SITE_URL);
           }
    }

    public function cart() {
      if(!isset($_POST['ajax'])) {  
        if(Session::hasItem('cart')) {
            $sess_cart = Session::getItem('cart', true);
            $is_empty_cart = (count($sess_cart['items']) == 0);
            $cart_total = Templater::total($sess_cart['total']);
            $items = Templater::cart($sess_cart['items']);
            $front_cart = Templater::frontCart();
            Render::view('cart', ['is_empty_cart' => $is_empty_cart,
                'cart_total' => $cart_total, 'items' => $items, 'front_cart' => $front_cart]);

        } else {
            Router::redirect(SITE_URL);
        }
      } else {
          Router::mime('application/json');
          $action = $_POST['ajax'];
          $output = [];
          switch($action) {
              case 'cart_remove':
                $output = $this->ajaxCartRemove();
                break;
                case 'cart_update':
                $output = $this->ajaxCartUpdate();
                break;  
              default:
                break;  
          }
          echo json_encode($output);
          exit;
      }  
    }

    public function checkout() {
        if(!isset($_POST['ajax'])) {
            if (Session::hasItem('cart')) {
                $sess_cart = Session::getItem('cart', true);
                $is_empty_cart = (count($sess_cart['items']) == 0);
                $cart_total = Templater::total($sess_cart['total']);
                $items = Templater::cart($sess_cart['items']);
                $vat = new Vat(VAT_VALUE);
                $cart_total_taxes = Templater::total($vat->add($sess_cart['total']));
                $front_cart = Templater::frontCart();
                Render::view('checkout',
                [
                   'is_empty_cart' => $is_empty_cart,
                   'cart_total' => $cart_total,
                   'items' => $items,
                   'cart_total_taxes' => $cart_total_taxes,
                   'front_cart' => $front_cart
                ]);

            } else {
                Router::redirect(SITE_URL);
            }
        } else {
            Router::mime('application/json');
            $output = $this->ajaxCheckout();
            echo json_encode($output);
            exit;
        }
    }

    public function payment() {
      if($_SERVER['REQUEST_METHOD'] === 'GET') {
          if (Session::hasItem('cart')) {

              $sess_cart = Session::getItem('cart', true);
              $is_empty_cart = (count($sess_cart['items']) == 0);
              if ($is_empty_cart) {
                  Router::redirect(SITE_URL);
              } else {
                  if (!USE_BT) {
                      $paypal = new PayPal();
                      $paypal->addItems($sess_cart['items']);
                      $settings = $paypal->getSettings();

                      extract($settings);

                      $cart_total = Templater::total($sess_cart['total']);
                      $items = Templater::cart($sess_cart['items']);
                      $vat = new Vat(VAT_VALUE);
                      $cart_total_taxes = Templater::total($vat->add($sess_cart['total']));
                      $order_id = Session::getItem('order_id');

                      $tax = $paypal->getTax($sess_cart['total']);

                      $paypal_items = $paypal->getItems();
                      $front_cart = Templater::frontCart();

                      Render::view('payment', [
                          'cart_total' => $cart_total,
                          'items' => $items,
                          'cart_total_taxes' => $cart_total_taxes,
                          'order_id' => $order_id,
                          'tax' => $tax,
                          'paypal_items' => $paypal_items,
                          'front_cart' => $front_cart,
                          'formurl' => PP_FORM_URL,
                          'business' => PP_BUSINESS,
                          'currency' => PP_CURRENCY,
                          'location' => PP_LOCATION,
                          'returnurl' => PP_RETURN_URL,
                          'returntxt' => PP_RETURN_TXT,
                          'cancelurl' => PP_CANCEL_URL,
                          'shipping' => PP_SHIPPING
                      ]);
                  } else {
                      $bt_gateway = new Gateway([
                          'environment' => BT_ENVIRONMENT,
                          'merchantId' => BT_MERCHANT_ID,
                          'publicKey' => BT_PUBLIC_KEY,
                          'privateKey' => BT_PRIVATE_KEY
                      ]);
                      $bt_token = $bt_gateway->ClientToken()->generate();

                      $cart_total = Templater::total($sess_cart['total']);
                      $items = Templater::cart($sess_cart['items']);
                      $vat = new Vat(VAT_VALUE);
                      $cart_total_taxes = Templater::total($vat->add($sess_cart['total']));
                      $order_id = Session::getItem('order_id');
                      $front_cart = Templater::frontCart();

                      Render::view('payment-bt', [
                          'cart_total' => $cart_total,
                          'items' => $items,
                          'cart_total_taxes' => $cart_total_taxes,
                          'order_id' => $order_id,
                          'front_cart' => $front_cart,
                          'token' => $bt_token,
                          'amount' => Templater::rawTotal($vat->add($sess_cart['total']))
                      ]);
                  }
              }

          } else {
              Router::redirect(SITE_URL);
          }
      } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
          if (Session::hasItem('cart')) {
              $amount = $_POST['amount'];
              $nonce = $_POST['payment_method_nonce'];
              $bt_gateway = new Gateway([
                  'environment' => BT_ENVIRONMENT,
                  'merchantId' => BT_MERCHANT_ID,
                  'publicKey' => BT_PUBLIC_KEY,
                  'privateKey' => BT_PRIVATE_KEY
              ]);
              $result = $bt_gateway->transaction()->sale([
                  'amount' => $amount,
                  'paymentMethodNonce' => $nonce,
                  'options' => [
                      'submitForSettlement' => true
                  ]
              ]);
              if ($result->success || !is_null($result->transaction)) {
                  $transaction = $result->transaction;
                  Router::redirect(SITE_URL . '/transaction/' . $transaction->id);
              } else {
                  $errors = [];

                  foreach($result->errors->deepAll() as $error) {
                      $errors[$error->code] = $error->message;
                  }

                  Router::redirect(SITE_URL . 'payment/?' . http_build_query($errors));
              }
          } else {
              Router::redirect(SITE_URL);
          }
      }
    }

    public function transaction($args) {
        $bt_gateway = new Gateway([
            'environment' => BT_ENVIRONMENT,
            'merchantId' => BT_MERCHANT_ID,
            'publicKey' => BT_PUBLIC_KEY,
            'privateKey' => BT_PRIVATE_KEY
        ]);

        $transactionSuccessStatuses = [
            Transaction::AUTHORIZED,
            Transaction::AUTHORIZING,
            Transaction::SETTLED,
            Transaction::SETTLING,
            Transaction::SETTLEMENT_CONFIRMED,
            Transaction::SETTLEMENT_PENDING,
            Transaction::SUBMITTED_FOR_SETTLEMENT
        ];

        $transaction = $bt_gateway->transaction()->find($args[0]);
        if (in_array($transaction->status, $transactionSuccessStatuses)) {
            Router::redirect(SITE_URL . 'thank-you/?st=completed');
        } else {
            Router::redirect(SITE_URL . 'payment/?st=' . $transaction->status);
        }
    }

    public function thankYou() {
        if(Session::hasItem('order_id')) {
            $order_id = Session::getItem('order_id');
            $db = new Database();
            $query_order = "SELECT * FROM orders WHERE id = '$order_id'";
            $order_res = $db->select($query_order);
            $order_data = $order_res[0];
            $query_customer = "SELECT * FROM customers WHERE id = '" . $order_data['customer'] . "'";
            $customer_res = $db->select($query_customer);
            $customer = ['fullname' => $customer_res[0]['firstname'] . ' ' . $customer_res[0]['lastname'], 'email' => $customer_res[0]['email']];

            if(Validator::isPayPalRequest()) {

                if (Mail::sendConfirmation($order_data, $customer)) {
                    if ($db->query("UPDATE orders SET status = 1 WHERE id = '$order_id'")) {
                        Session::end();
                    }
                }

                Render::view('thank-you', [
                ]);

            } else {
                Router::redirect(SITE_URL . 'payment/');
            }

        } else {
            Router::redirect(SITE_URL);
        }
    }

    public function cancel() {
        if(Session::hasItem('order_id')) {
            Router::redirect(SITE_URL . 'payment/');
        } else {
            Router::redirect(SITE_URL);
        }
    }
}