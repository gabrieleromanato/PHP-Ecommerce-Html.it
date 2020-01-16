<?php

class Shop  {
    public $database;

    public function __construct() {
        $this->database = new Database();
    }

    protected function processCheckout($data) {
        $customer = new Customer(uniqid(), $data['billing']['firstname'], $data['billing']['lastname'], $data['billing']['email']);
        if($customer->save()) {
            $sess_cart = unserialize($_SESSION['cart']);
            $cart = new Cart();
            $cart->setItems($sess_cart['items']);
            $cart->setTotal($sess_cart['total']);

            $order = new Order(uniqid(),$cart, $customer);
            $order->setBilling($data['billing']['address']);
            $shipping = (isset($data['shipping'])) ? $data['shipping']['address'] : $data['billing']['address'];
            $order->setShipping($shipping);

            if($order->save()) {
                $_SESSION['order_id'] = $order->getId();
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
        $sess_cart = unserialize($_SESSION['cart']);
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
        $_SESSION['cart'] = serialize($session_cart_upd);

        $output = ['status' => 'ok'];
        return $output;
    }

    protected function ajaxCartRemove() {
        $id = $_POST['id'];
        $prod_id = $this->database->escape($id);
        $product = $this->database->select("SELECT * FROM products WHERE id = $prod_id");
        $cart_product = new Product($product[0]['id'], $product[0]['title'], $product[0]['description'], floatval($product[0]['price']), $product[0]['manufacturer'], $product[0]['image'], $product[0]['slug']);
        $sess_cart = unserialize($_SESSION['cart']);
        $cart = new Cart();
        $cart->setItems($sess_cart['items']);
        $cart->setTotal($sess_cart['total']);
        $cart->removeFromCart($cart_product);
        $session_cart_upd = ['items' => $cart->getItems(), 'total' => $cart->getTotal()];
        $_SESSION['cart'] = serialize($session_cart_upd);
        
        $output = ['status' => 'ok'];
        return $output;
    }

    public function index() {
        $title = 'PHP E-commerce';
        $products = Templater::products($this->database->select("SELECT * FROM products WHERE price > 0 ORDER BY rand() LIMIT 9"));
        $front_cart = Templater::frontCart();
        include(ABSPATH . 'views/home.php');
    }

    public function product() {
        if(!Validator::isProductId($_GET['id'])) {
            Router::redirect(SITE_URL);
        } else {
            $id = $_GET['id'];
            if($this->database->exists($id)) {
                $prod_id = $this->database->escape($id);
                $product = $this->database->select("SELECT * FROM products WHERE id = $prod_id");
                $displayProduct = Templater::products($product);
                $singleProduct = $displayProduct[0];
                $front_cart = Templater::frontCart();
                $title = $singleProduct['name'];
                include(ABSPATH . 'views/product.php');
            } else {
                Router::status('404', 'Not Found');
                $title = '404: Not Found';
                include(ABSPATH . 'views/404.php');
            }
        }
    }

    public function addToCart() {

           if(Validator::isCartAddRequest($_REQUEST['id'], $_REQUEST['quantity'])) {

                $id = $_REQUEST['id'];
                $quantity = $_REQUEST['quantity'];

                if($this->database->exists($id)) {
                    $product = $this->database->select("SELECT * FROM products WHERE id = $id");
                    $qty = (int) $quantity;
                    if(class_exists('Product')) {
                        $cart_product = new Product($product[0]['id'], $product[0]['title'], $product[0]['description'], floatval($product[0]['price']), $product[0]['manufacturer'], $product[0]['image'], $product[0]['slug']);
                        if(!isset($_SESSION['cart'])) {
                            $cart = new Cart();
                            $cart->addToCart($cart_product, $qty);
                            $session_cart = ['items' => $cart->getItems(), 'total' => $cart->getTotal()];
                            $_SESSION['cart'] = serialize($session_cart);
                        } else {
                            $sess_cart = unserialize($_SESSION['cart']);
                            $cart = new Cart();
                            $cart->setItems($sess_cart['items']);
                            $cart->setTotal($sess_cart['total']);
                            $cart->addToCart($cart_product, $qty);
                            $session_cart_upd = ['items' => $cart->getItems(), 'total' => $cart->getTotal()];
                            $_SESSION['cart'] = serialize($session_cart_upd);
                        }
                        Router::redirect(SITE_URL . 'cart/');
                    } else {
                        Router::redirect(SITE_URL);
                    }
                } else {
                    Router::redirect(SITE_URL);
                }

           } else {
               Router::redirect(SITE_URL);
           }
    }

    public function cart() {
      if(!isset($_POST['ajax'])) {  
        if(isset($_SESSION['cart'])) {
            $sess_cart = unserialize($_SESSION['cart']);
            $is_empty_cart = (count($sess_cart['items']) == 0);
            $cart_total = Templater::total($sess_cart['total']);
            $items = Templater::cart($sess_cart['items']);
            $front_cart = Templater::frontCart();
            $title = 'Cart';
            include(ABSPATH . 'views/cart.php');

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
            if (isset($_SESSION['cart'])) {
                $sess_cart = unserialize($_SESSION['cart']);
                $is_empty_cart = (count($sess_cart['items']) == 0);
                $cart_total = Templater::total($sess_cart['total']);
                $items = Templater::cart($sess_cart['items']);
                $vat = new Vat(VAT_VALUE);
                $cart_total_taxes = Templater::total($vat->add($sess_cart['total']));
                $title = 'Checkout';
                $front_cart = Templater::frontCart();
                include(ABSPATH . 'views/checkout.php');

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
        if (isset($_SESSION['cart'])) {

            $sess_cart = unserialize($_SESSION['cart']);
            $is_empty_cart = (count($sess_cart['items']) == 0);
            if($is_empty_cart) {
                Router::redirect(SITE_URL);
            } else {
                $title = 'Payment';
                $paypal = new PayPal();
                $paypal->addItems($sess_cart['items']);
                $settings = $paypal->getSettings();

                extract($settings);

                $cart_total = Templater::total($sess_cart['total']);
                $items = Templater::cart($sess_cart['items']);
                $vat = new Vat(VAT_VALUE);
                $cart_total_taxes = Templater::total($vat->add($sess_cart['total']));
                $order_id = $_SESSION['order_id'];

                $tax = $paypal->getTax($sess_cart['total']);

                $paypal_items = $paypal->getItems();
                $front_cart = Templater::frontCart();

                include(ABSPATH . 'views/payment.php');
            }

        } else {
            Router::redirect(SITE_URL);
        }
    }

    public function thankYou() {
        if(isset($_SESSION['order_id'])) {
            $order_id = $_SESSION['order_id'];
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
                        session_destroy();
                        $_SESSION = [];
                    }
                }

                include(ABSPATH . 'views/thank-you.php');

            } else {
                Router::redirect(SITE_URL . 'payment/');
            }

        } else {
            Router::redirect(SITE_URL);
        }
    }

    public function cancel() {
        if(isset($_SESSION['order_id'])) {
            Router::redirect(SITE_URL . 'payment/');
        } else {
            Router::redirect(SITE_URL);
        }
    }
}