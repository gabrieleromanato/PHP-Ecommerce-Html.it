<!DOCTYPE html>
<html>
<head>
<title><?= $title; ?></title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" media="screen" href="/public/assets/css/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="/public/assets/css/font-awesome/css/all.css">
<link rel="stylesheet" type="text/css" media="screen" href="/public/assets/css/style.css">
</head>
<body class="<?= $deviceClass; ?>">

<header id="site-header">
    <?php if($deviceClass == 'desktop'): ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">PHP Ecommerce</a>
        <div class="collapse navbar-collapse">
        <ul class="navbar-nav">
            <li class="nav-item">
                 <a class="nav-link" href="/">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/manufacturers"><?= $locale['menu']['manufacturers']; ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/lang/it">IT</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/lang/en">EN</a>
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
                        <div class="front-cart-footer-wrap">
                            <div class="mb-4">
                                <strong><?= $locale['form']['total']; ?>:</strong> <?= $front_cart_total; ?>
                            </div>
                            <div class="mt-4">
                                <a href="/cart" class="btn btn-success btn-block"><?= $locale['buttons']['view']; ?></a>
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
        </ul>
        </div>
    </nav>

    <?php else: ?>

    <nav id="mobile-nav" class="bg-light">

        <a href="#" id="open-menu" class="text-dark"><i class="fas fa-bars"></i></a>

        <ul id="mobile-menu">
            <li>
                <a href="/" class="text-dark">Home</a>
            </li>
            <li>
                <a href="/manufacturers" class="text-dark"><?= $locale['menu']['manufacturers']; ?></a>
            </li>
            <li>
                <a href="/lang/it" class="text-dark">IT</a>
            </li>
            <li>
                <a href="/lang/en" class="text-dark">EN</a>
            </li>

        </ul>

        <?php if(count($front_cart) > 0): $front_cart_items = $front_cart['cart']['items']; $front_cart_total = $front_cart['cart']['total']; ?>

            <a href="/cart" class="text-dark">
                <i class="fas fa-shopping-cart"></i>
                <span><?php echo count($front_cart_items); ?></span> / <span><?= $front_cart_total; ?></span>
            </a>
        <?php endif; ?>

    </nav>

    <?php endif; ?>
    
</header>

<div id="site" class="container">
    <form action="/search" method="get" id="search-form" class="form-inline text-center mt-lg-3 mb-lg-3">
        <div class="form-group">
            <input type="text" class="form-control form-control-lg" name="s" id="s" placeholder="<?= $locale['buttons']['search']; ?>">
            <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-search"></i></button>
        </div>
    </form>