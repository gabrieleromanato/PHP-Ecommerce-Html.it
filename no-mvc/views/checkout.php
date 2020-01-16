<?php include('header.php'); ?>

<?php if(!$is_empty_cart): ?>

<form action="/checkout" method="post" id="form-checkout" novalidate>

<div class="row mt-5 mb-5">

    <div class="col-md-6" id="billing-fields">
        <h2>Billing</h2>
        <div class="form-group mb-5">
            <label for="billing_firstname">First Name</label>
            <input type="text" name="billing_firstname" id="billing_firstname" class="form-control">
        </div>
        <div class="form-group mb-5">
            <label for="billing_lastname">Last Name</label>
            <input type="text" name="billing_lastname" id="billing_lastname" class="form-control">
        </div>
        <div class="form-group mb-5">
            <label for="billing_email">E-mail</label>
            <input type="email" name="billing_email" id="billing_email" class="form-control">
        </div>
        <div class="form-group mb-5">
            <label for="billing_address">Address</label>
            <input type="text" name="billing_address" id="billing_address" class="form-control">
        </div>
    </div>

    <div class="col-md-6" id="shipping-fields">
        <h2>Shipping</h2>
        <div class="form-group mb-5">
            <label for="shipping_firstname">First Name</label>
            <input type="text" name="shipping_firstname" id="shipping_firstname" class="form-control">
        </div>
        <div class="form-group mb-5">
            <label for="shipping_lastname">Last Name</label>
            <input type="text" name="shipping_lastname" id="shipping_lastname" class="form-control">
        </div>
        <div class="form-group mb-5">
            <label for="shipping_email">E-mail</label>
            <input type="email" name="shipping_email" id="shipping_email" class="form-control">
        </div>
        <div class="form-group mb-5">
            <label for="shipping_address">Address</label>
            <input type="text" name="shipping_address" id="shipping_address" class="form-control">
        </div>
    </div>

    

</div>

<p class="mb-5 text-center"><label for="same-billing"><input type="checkbox" id="same-billing" name="same-billing" value="1"> Same as billing</label></p>


<h2>Order summary</h2>

<table class="table table-bordered">

    <thead>
        <tr>
            <th scope="col">Product</th>
            <th scope="col">Price</th>
            <th scope="col">Quantity</th>
            <th scope="col">Subtotal</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($items as $item): ?>
        <tr>
            <td><?= $item['name']; ?></td>
            <td><?= $item['price']; ?></td>
            <td><?= $item['quantity']; ?></td>
            <td><?= $item['subtotal']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <td colspan="2">
            <b>Total:</b> <?= $cart_total; ?>
        </td>
        <td colspan="2">
            <b>Total with taxes:</b> <?= $cart_total_taxes; ?>
        </td>
    </tfoot>

</table>
<div class="clearfix mt-5">
    <button type="submit" name="send-order" value="1" class="btn btn-success">Finalize order</button>
</div>

</form>

<?php else: ?>

<p class="alert alert-info">Your cart is empty</p>

<?php endif; ?>

<?php include('footer.php'); ?>