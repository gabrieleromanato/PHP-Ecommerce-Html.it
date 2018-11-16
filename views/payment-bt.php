<?php include('header.php'); ?>



    <form action="/payment" method="post" id="form-payment" novalidate>
        <h2 class="mb-5"><?= $locale['form']['payment']; ?></h2>
        <h3 class="mb-5"><?= $locale['form']['order_id']; ?>: <?= $order_id ?></h3>
        <h3 class="mb-5"><?= $locale['form']['order_summary']; ?></h3>

        <table class="table table-bordered">

            <thead>
            <tr>
                <th scope="col"><?= $locale['form']['product']; ?></th>
                <th scope="col"><?= $locale['form']['price']; ?></th>
                <th scope="col"><?= $locale['form']['quantity']; ?></th>
                <th scope="col"><?= $locale['form']['subtotal']; ?></th>
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
                <b><?= $locale['form']['total']; ?>:</b> <?= $cart_total; ?>
            </td>
            <td colspan="2">
                <b><?= $locale['form']['total_taxes']; ?>:</b> <?= $cart_total_taxes; ?>
            </td>
            </tfoot>

        </table>

        <div class="bt-drop-in-wrapper">
            <div id="bt-dropin"></div>
        </div>

        <p class="mt-5">
            <input type="hidden" id="amount" name="amount" value="<?= $amount; ?>">
            <input id="nonce" name="payment_method_nonce" type="hidden">
            <input type="submit" value="<?= $locale['buttons']['braintree']; ?>" class="btn btn-primary btn-lg">
        </p>
    </form>
    <script src="https://js.braintreegateway.com/web/dropin/1.14.0/js/dropin.min.js"></script>
    <script>
        var form = document.querySelector('#form-payment');
        var client_token = "<?= $token; ?>";

        braintree.dropin.create({
            authorization: client_token,
            selector: '#bt-dropin',
            paypal: {
                flow: 'vault'
            }
        }, function (createErr, instance) {
            if (createErr) {
                console.log('Create Error', createErr);
                return;
            }
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                instance.requestPaymentMethod(function (err, payload) {
                    if (err) {
                        console.log('Request Payment Method Error', err);
                        return;
                    }
                    document.querySelector('#nonce').value = payload.nonce;
                    form.submit();
                });
            });
        });
    </script>

<?php include('footer.php'); ?>