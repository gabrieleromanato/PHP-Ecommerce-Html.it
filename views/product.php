<?php include('header.php'); ?>

<article id="product">
        <header class="mb-5">
            <h1><?= $singleProduct['name']; ?></h1>
</header>
<figure>
                <img src="<?= $singleProduct['image']; ?>" alt="" class="img-fluid">
            </figure>    
            <div class="row mt-4 mb-4">
                <div class="col-md-2">
                    <?= $singleProduct['price']; ?>
                </div>
                <div class="col-md-6">
                <form class="form-inline" action="/add-to-cart" method="post">
                    <div class="form-group">
                        <input type="number" name="quantity" value="1" min="1" step="1" class="form-control qty">
                        <input type="hidden" name="id" value="<?= $singleProduct['id']; ?>">
                        <input type="submit" class="btn btn-success" value="<?= $locale['buttons']['add']; ?>">
                    </div>
                </form>
                </div>
            </div>
            <p><?= $singleProduct['description']; ?></p>
</article>    

<?php include('footer.php'); ?>