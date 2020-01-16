<?php include('header.php');?>

<section class="row products">
    <?php if(isset($products) && is_array($products) && count($products) > 0): ?>

    <?php foreach($products as $product): ?>

    <article class="col-md-4 product">
        <header>
            <h3><?= $product['name']; ?></h3>
        </header>
        <figure>
            <a href="<?= $product['link']; ?>">
                <img src="<?= $product['image']; ?>" class="img-fluid" alt="">
            </a>    
        </figure>
        <p class="text-muted text-truncate"><?= $product['description']; ?></p>
        <footer class="row">
            <div class="col-md-6"><?= $product['price']; ?></div>
                <div class="text-right col-md-6">
                    <a href="<?= $product['add_to_cart_link']; ?>" class="btn btn-success">Add to cart</a>
                </div>
        </footer>
    </article>

    <?php endforeach; ?>

    <?php else: ?>

    <p class="alert alert-info">No products to display.</p>

    <?php endif; ?>
</section

<?php include('footer.php'); ?>