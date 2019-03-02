<?php

if (empty('REX_LINK[1]')) {
    if (rex::isBackend()) {
        echo 'Bitte den Link zu den Optionen setzen!';
    }
} elseif (!rex::isBackend() && !empty('REX_LINK[1]')) {
    // Get Session vars
    $session = rex_session('order', 'array', '');

    $rpcData = new PrintConfiguratorData();
    $data = $rpcData->getData();
    $order_flat_charge = $data['dom_elements']['order_flat_charge'];

    $sidebar = '
    <aside class="price-sidebar">
        <div class="mb-3">
            <h5>Generell</h5>
            <div class="order-flat_charge">'
               .$order_flat_charge.
               '</div>
            <div class="order-data_check">
            </div>
        </div>
        <div class="mb-3">
            <h5 class="pt-2">Papier &amp; Druck</h5>
            <div class="order-paper">
            </div>
        </div>
        <div class="mb-3">
            <h5 class="pt-2">Bindung &amp; Optionen</h5>
            <div class="order-fixation">
            </div>
            <div class="order-fixation-two">
            </div>
        </div>
        <div class="order-subtotal my-3">
        </div>
    </aside>';

    echo '<h1>Print Configurator</h1>';
    echo $sidebar;
} else {
    echo 'Nope';
}
