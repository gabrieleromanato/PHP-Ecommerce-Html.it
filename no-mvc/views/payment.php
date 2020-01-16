<?php include('header.php'); ?>



    <form action="<?= $formurl; ?>" method="post" id="form-payment" novalidate>
        <h2 class="mb-5">Payment</h2>
        <h3 class="mb-5">Order ID: <?= $order_id ?></h3>
        <h3 class="mb-5">Order summary</h3>

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

        <input name="cmd" value="_cart" type="hidden">
        <input name="upload" value="1" type="hidden">
        <input name="no_note" value="0" type="hidden">
        <input name="bn" value="RG_BuyNow_WPS_IT" type="hidden">
        <input name="tax_cart" value="<?= str_replace(',', '.', $tax); ?>" type="hidden">
        <input name="rm" value="2" type="hidden">
        <input name="business" value="<?= $business; ?>" type="hidden">
        <input name="handling_cart" value="0" type="hidden">
        <input name="currency_code" value="<?= $currency; ?>" type="hidden">
        <input name="lc" value="<?= $location; ?>" type="hidden">
        <input name="return" value="<?= $returnurl; ?>" type="hidden">
        <input name="cbt" value="<?= $returntxt; ?>" type="hidden">
        <input name="cancel_return" value="<?= $cancelurl; ?>" type="hidden">

        <?php $n = 0; foreach($paypal_items as $it): $n++; ?>

            <div>
                <input name="item_name_<?= $n; ?>" value="<?= $it['name']; ?>" type="hidden">
                <input name="quantity_<?= $n; ?>" value="<?= $it['quantity']; ?>" type="hidden">
                <input name="amount_<?= $n; ?>" value="<?= $it['price']; ?>" type="hidden">
                <input name="shippping_<?= $n; ?>" value="<?= $shipping; ?>" type="hidden">
            </div>

        <?php endforeach; ?>

        <p class="mt-5">
            <input type="submit" value="Pay with PayPal" class="btn btn-primary btn-lg">
        </p>
    </form>

<?php include('footer.php'); ?>