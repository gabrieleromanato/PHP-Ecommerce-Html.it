<?php include('header.php'); ?>

<?php if(!$is_empty_cart): ?>

<form action="/cart" method="post" id="form-cart">

<table class="table table-bordered">

    <thead>
        <tr>
            <th scope="col" colspan="2">Product</th>
            <th scope="col">Price</th>
            <th scope="col">Quantity</th>
            <th scope="col" colspan="2">Subtotal</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($items as $item): ?>
        <tr>
            <td><img src="<?= $item['image']; ?>" alt="" class="img-thumbnail"></td>
            <td><a href="<?= $item['link']; ?>"><?= $item['name']; ?></a></td>
            <td><?= $item['price']; ?></td>
            <td><input type="number" name="quantity[]" data-id="<?= $item['id']; ?>" step="1" min="1" class="form-control qty" value="<?= $item['quantity']; ?>"></td>
            <td><?= $item['subtotal']; ?></td>
            <td><a href="/cart/?remove-item=<?= $item['id']; ?>" class="text-danger remove-cart-item">&times;</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <td colspan="6">
            <b>Total:</b> <?= $cart_total; ?>
        </td>
    </tfoot>

</table>
<div class="clearfix mt-5">
    <button type="submit" name="update-cart" value="1" class="btn btn-success float-left">Update cart</button>
    
    <a href="/checkout/" class="btn btn-success float-right">Proceed to checkout</a>
    <a href="/" class="btn btn-success float-right mr-5">Continue shopping</a>
</div>
</form>
<?php else: ?>

<p class="alert alert-info">Your cart is empty</p>

<?php endif; ?>

<?php include('footer.php'); ?>