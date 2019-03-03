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
    dump($data);
    $order_flat_charge = $data['dom_elements']['order_flat_charge'];
    $page_prices = $data['formatted_basics']['page_prices'];
    dump($page_prices);

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
    echo '<div class="container">';
    $yform = new rex_yform();
    $yform->setDebug(true);
    $yform->setObjectparams('real_field_names', 1);
    $yform->setObjectparams('article_id', rex_article::getCurrentId());
    $yform->setActionField('callback', array('PrintConfigurator::setOrder'));
    $yform->setActionField('redirect', array('REX_LINK[1]'));

    // open left area
    $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
    $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-8">'));

    // paper and print area
    $yform->setValueField('fieldset', array('paper_and_print', 'Papier &amp; Druck'));
    foreach ($page_prices as $key => $page_price) {
        $yform->setValueField('html', array('html', 'HTML', '<div class="slider" id="'.$key.'"></div>'));
        $yform->setValueField('text', array(
            $page_price['type'],
            $page_price['name'],
            $page_price['min'],
            '',
            '{"data-min":"'.$page_price['min'].'","data-max":"'.$page_price['max'].'","data-start":"'.$page_price['start'].'","data-price":"'.$page_price['price'].'","data-vat":"'.$page_price['vat'].'","disabled":"disabled"}',
        ));
    }

    $yform->setValueField('html', array('html', 'HTML', '</div>'));
    $yform->setValueField('fieldset', array('sidebar', '', 'col-12 col-md-4'));
    $yform->setValueField('html', array('html', 'HTML', $sidebar));
    $yform->setValueField('fieldset', array('sidebar', '', 'col-2', 'onlyclose'));
    $yform->setValueField('html', array('html', 'HTML', '</div>'));
    $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
    $yform->setValueField('html', array('html', 'HTML', '<div class="col-12">'));
    $yform->setValueField('submit', array('submit', 'Bestellung aufgeben'));
    $yform->setValueField('html', array('html', 'HTML', '</div>'));
    $yform->setValueField('html', array('html', 'HTML', '</div>'));
    echo $yform->getForm();
    echo '</div>';
} else {
    echo 'Nope';
}
