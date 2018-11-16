<?php include('header.php');?>

    <section class="row products">
        <?php if(isset($manufacturers) && is_array($manufacturers) && count($manufacturers) > 0): ?>

            <?php foreach($manufacturers as $m): ?>

                <article class="col-md-4 product">
                    <header>
                        <h3 class="text-center"><?= $m['name']; ?></h3>
                    </header>
                    <footer class="row">
                        <div class="text-center col-md-12">
                            <a href="<?= $m['link']; ?>" class="btn btn-success btn-block"><?= $locale['products']['view']; ?></a>
                        </div>
                    </footer>
                </article>

            <?php endforeach; ?>

        <?php else: ?>

            <p class="alert alert-info"><?= $locale['manufacturers']['none']; ?></p>

        <?php endif; ?>
    </section

<?php include('footer.php'); ?>