<?php include('header.php');?>

    <section class="row products">
        <?php if(isset($products) && is_array($products) && count($products) > 0): ?>

            <?php foreach($products as $product): ?>

                <article class="col-md-6 product">
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
                            <a href="<?= $product['add_to_cart_link']; ?>" class="btn btn-success"><?= $locale['buttons']['add']; ?></a>
                        </div>
                    </footer>
                </article>

            <?php endforeach; ?>



        <?php else: ?>

            <p class="alert alert-info col-md-12"><?= $locale['products']['none']; ?></p>

        <?php endif; ?>
    </section>

    <nav id="pagination" class="row mt-lg-4">
        <ul class="pagination row justify-content-center">
            <?php for($i = 0; $i < $pages; $i++): $p = $i + 1; ?>

                <li class="page-item<?php if($p == $current_page): ?> active<?php endif; ?>"><a href="?s=<?= $s; ?>&page=<?= $p; ?>" class="page-link"><?= $p; ?></a></li>

            <?php endfor; ?>
        </ul>
    </nav>

<?php include('footer.php'); ?>