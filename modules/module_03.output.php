<?php

if (empty('REX_LINK[1]')) {
    if (rex::isBackend()) {
        echo 'Bitte den Link zur Seite mit den Optionen setzen!';
    }
} elseif (empty('REX_LINK[2]')) {
    if (rex::isBackend()) {
        echo 'Bitte den Link zur Seite mit dem Rechnungsdaten setzen!';
    }
} else {
    //check if session has value order - if not redirect to print configurator
    if (!isset($_SESSION) && !rex::isBackend()) {
        rex_redirect('REX_LINK[1]');
    } else {
        if (empty(rex_session('options', 'array', ''))) {
            rex_redirect('REX_LINK[1]');
        } else {
            $order_config = rex_session('order', 'array', '');
            $options_config = rex_session('options', 'array', '');

            //debug stuff
            dump($order_config);
            dump($options_config);

            $sidebar = '
    <aside class="price-sidebar">
        <div class="mb-3">
            <h5>Generell</h5>
            <div class="order-data_check">'.
                       $order_config['dom_elements']['order-data_check']
                       .'</div>
        </div>
        <div class="mb-3">
            <h5 class="pt-2">Papier &amp; Druck</h5>
            <div class="order-paper">'.
                       $order_config['dom_elements']['order-paper']
                       .'</div>
        </div>
        <div class="mb-3">
            <h5 class="pt-2">Bindung &amp; Optionen</h5>
            <div class="order-fixations">'.
                       $order_config['dom_elements']['order-fixations']
                       .'</div>
        </div>
        <div class="order-subtotal my-3">'.
                       $order_config['dom_elements']['order-subtotal']
                       .'</div>
    </aside>';

            echo '<div class="container"><div class="row"><div class="col-12">';
            echo '<h1>Lieferung</h1>';
            echo '</div></div></div>';
            echo '<div class="container">';

            $yform = new rex_yform();
            $yform->setDebug(true);
            $yform->setObjectparams('real_field_names', 1);
            $yform->setObjectparams('article_id', rex_article::getCurrentId());
            $yform->setRedaxoVars(REX_ARTICLE_ID);
            $yform->setActionField('callback', array('PrintConfigurator::setAddress'));
            $yform->setActionField('redirect', array('REX_LINK[2]'));
            $yform->setObjectparams('form_name', 'options_form');

            // open left area
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-8">'));

            //data area
            $yform->setValueField('fieldset', array('delivery_address', 'Lieferadresse'));
            $yform->setValueField('choice', ["salutation", "", "Frau,Herr,Divers", 1, 0, "Frau"]);
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('firstname', 'Vorname',  $options_config['firstname'],  '',  '{"placeholder":"Vorname","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('lastname', 'Nachname',  $options_config['lastname'],  '',  '{"placeholder":"Nachname","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-8">'));
            $yform->setValueField('text', array('street', 'Straße',  '',  '',  '{"placeholder":"Straße","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-4">'));
            $yform->setValueField('text', array('street_no', 'Nr.',  '',  '',  '{"placeholder":"Nr.","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('text', array('additional_info', 'Zusatz',  '',  '',  '{"placeholder":"z.B. Etage, Firma, etc."}'));

            $yform->setValueField('choice', ["delivery_address_same_as_billing_address", "", "Rechnungsadresse entspricht Lieferadresse", 1, 1]);

            $yform->setValueField('fieldset', array('billing_address', 'Rechnungsadresse'));
            $yform->setValueField('choice', ["billing_salutation", "", "Frau,Herr,Divers", 1, 0, "Frau"]);
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('billing_firstname', 'Vorname',  $options_config['firstname'],  '',  '{"placeholder":"Vorname","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('billing_lastname', 'Nachname',  $options_config['lastname'],  '',  '{"placeholder":"Nachname","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-8">'));
            $yform->setValueField('text', array('billing_street', 'Straße',  '',  '',  '{"placeholder":"Straße","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-4">'));
            $yform->setValueField('text', array('billing_street_no', 'Nr.',  '',  '',  '{"placeholder":"Nr.","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('text', array('billing_additional_info', 'Zusatz',  '',  '',  '{"placeholder":"z.B. Etage, Firma, etc."}'));

            $yform->setValueField('fieldset', array('contact_infos', 'Kontaktinformationen'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('email', ['email','E-Mail-Adresse', '',  '', '{"placeholder":"E-Mail Adresse","required":"required"}']);
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('email', ['email_repeat','E-Mail-Adresse bestätigen', '',  '', '{"placeholder":"E-Mail Adresse wiederholen","required":"required"}']);
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('text', array('phone', 'Telefon / Mobilnummer',  '',  '',  '{"placeholder":"z.B. 0661 41 09 51 51"}'));

            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('fieldset', array('sidebar', '', 'col-12 col-md-4'));
            $yform->setValueField('html', array('html', 'HTML', $sidebar));
            $yform->setValueField('fieldset', array('sidebar', '', 'col-2', 'onlyclose'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 d-flex">'));
            $yform->setValueField('html', array('html', 'HTML', '<a class="btn btn-secondary" href="'.rex_getUrl('REX_LINK[1]').'">Zurück</a>'));
            $yform->setValueField('submit', array('submit', 'weiter', 'Weiter', '', '', 'btn-primary ml-auto'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            echo $yform->getForm();
            echo '</div>';

        }
    }
}
