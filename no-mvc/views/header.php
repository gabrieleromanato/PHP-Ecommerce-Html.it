<!DOCTYPE html>
<html>
<head>
<title><?= $title; ?></title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" media="screen" href="/css/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="/css/font-awesome/css/all.css">
<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css">
</head>
<body>

<header id="site-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">PHP Ecommerce</a>
        <div class="collapse navbar-collapse">
        <ul class="navbar-nav">
            <li class="nav-item">
                 <a class="nav-link" href="/">Home</a>
            </li>
            <?php if(count($front_cart) > 0): $front_cart_items = $front_cart['cart']['items']; $front_cart_total = $front_cart['cart']['total']; ?>
            <li class="nav-item" id="front-cart">
                <a href="#front-cart-contents" class="text-dark nav-link" id="front-cart-trigger">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="front-cart-count"><?php echo count($front_cart_items); ?></span>
                </a>
                <ul id="front-cart-contents">
                    <?php foreach($front_cart_items as $front_cart_item): ?>
                    <li>
                        <div class="row">
                            <figure class="col-md-3">
                                <a href="<?= $front_cart_item['link']; ?>">
                                    <img src="<?= $front_cart_item['image']; ?>" alt="" class="img-fluid img-thumbnail">
                                </a>
                            </figure>
                            <div class="col-md-9">
                                <h5><a href="<?= $front_cart_item['link']; ?>" class="text-dark"><?= $front_cart_item['name']; ?></a></h5>
                                <p class="mt-3 text-muted"><?= $front_cart_item['price']; ?> &times; <?= $front_cart_item['quantity']; ?></p>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <li id="front-cart-contents-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Total: </strong> <?= $front_cart_total; ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="/cart" class="btn btn-success">View cart</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
        </div>
    </nav>
    
</header>

<div id="site" class="container">