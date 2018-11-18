<?php include('header.php');?>

    <section class="row products">
        <?php if(isset($manufacturers) && is_array($manufacturers) && count($manufacturers) > 0): ?>

            <?php foreach($manufacturers as $m): ?>

                <article class="col-md-6 product">
                    <header>
                        <h3><?= $m['name']; ?></h3>
                    </header>
                    <figure>
                        <a href="<?= $m['link']; ?>">
                            <img src="<?= $m['image']; ?>" class="img-fluid" alt="">
                        </a>
                    </figure>
                    <footer class="row">
                        <div class="text-center col-md-12">
                            <a href="<?= $m['link']; ?>" class="btn btn-success btn-block"><?= $locale['products']['view']; ?></a>
                        </div>
                    </footer>
                </article>

            <?php endforeach; ?>

        <?php else: ?>

            <p class="alert alert-info col-md-12"><?= $locale['manufacturers']['none']; ?></p>

        <?php endif; ?>
    </section>

    <nav id="pagination" class="row mt-lg-4">
        <ul class="pagination row justify-content-center">
            <?php
                $previous = ( $current_page > 1 ) ? $current_page - 1 : null;
                $next = ( $current_page < $pages ) ? $current_page + 1 : null;
            ?>
                <?php if(!is_null($previous)): ?>

                <li class="page-item"><a href="?page=<?= $previous; ?>" class="page-link">&lt;</a></li>

                <?php endif; ?>

            <?php if(!is_null($next)): ?>

            <li class="page-item"><a href="?page=<?= $next; ?>" class="page-link">&gt;</a></li>

            <?php endif; ?>
        </ul>
    </nav>

<?php include('footer.php'); ?>