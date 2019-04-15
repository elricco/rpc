<?php

if (empty('REX_LINK[1]')) {
    if (rex::isBackend()) {
        echo 'Bitte den Link zur Seite mit dem Konfigurator setzen!';
    }
} elseif (empty('REX_LINK[2]')) {
    if (rex::isBackend()) {
        echo 'Bitte den Link zur Seite mit dem Rechnungsdaten setzen!';
    }
} else {
    //check if session has value order - if not redirect to print configurator
    if ((PHP_SESSION_ACTIVE != session_status()) && !rex::isBackend()) {
        rex_redirect('REX_LINK[1]');
    } else {
        if (empty(rex_session('order', 'array', ''))) {
            rex_redirect('REX_LINK[1]');
        } else {
            $session = rex_session('order', 'array', '');
            $rpcData = new PrintConfiguratorData();
            $data = $rpcData->getData();

            //debug stuff
            dump($session);
            dump($data);

            $title_options = 'Daten Titelseite & CD/DVD';
            $sidebar = '
    <aside class="price-sidebar">
        <div class="mb-3">
            <h5>Generell</h5>
            <div class="order-data_check">'.
               $session['dom_elements']['order-data_check']
            .'</div>
        </div>
        <div class="mb-3">
            <h5 class="pt-2">Papier &amp; Druck</h5>
            <div class="order-paper">'.
               $session['dom_elements']['order-paper']
           .'</div>
        </div>
        <div class="mb-3">
            <h5 class="pt-2">Bindung &amp; Optionen</h5>
            <div class="order-fixations">'.
               $session['dom_elements']['order-fixations']
           .'</div>
        </div>
        <div class="order-subtotal my-3">'.
           $session['dom_elements']['order-subtotal']
       .'</div>
    </aside>';

            echo '<div class="container"><div class="row"><div class="col-12">';
            echo '<h1>Optionen</h1>';
            echo '</div></div></div>';
            echo '<div class="container">';

            $yform = new rex_yform();
            $yform->setDebug(true);
            $yform->setObjectparams('real_field_names', 1);
            $yform->setObjectparams('article_id', rex_article::getCurrentId());

            $yform->setActionField('redirect', array('REX_LINK[2]'));
            $yform->setObjectparams('form_name', 'options_form');

            // open left area
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-8">'));

            // data area
            $yform->setValueField('fieldset', array('name_and_number', $title_options));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('firstnname', 'Vorname'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('lastnname', 'Nachname'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-9">'));
            $yform->setValueField('text', array('student_number', 'Matrikelnummer'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-3">'));
            $yform->setValueField('date', array('date', 'Jahr', '2000', '+25', 'YYYY', '1', '', 'select'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('text', array('type_of_work', 'Art'));
            $yform->setValueField('text', array('title', 'Titel'));

            $yform->setValueField('fieldset', array('spine_data', 'Daten Buchrücken'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('spine_firstnname', 'Vorname'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('spine_lastnname', 'Nachname'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('text', array('spine_type_of_work', 'Art'));
            $yform->setValueField('text', array('spine_title', 'Titel'));

            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('fieldset', array('sidebar', '', 'col-12 col-md-4'));
            $yform->setValueField('html', array('html', 'HTML', $sidebar));
            $yform->setValueField('fieldset', array('sidebar', '', 'col-2', 'onlyclose'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 d-flex">'));
            $yform->setValueField('html', array('html', 'HTML', '<a class="btn btn-secondary" href="">Zurück</a>'));
            $yform->setValueField('submit', array('submit', 'weiter', 'Weiter', '', '', 'btn-primary ml-auto'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            echo $yform->getForm();
            echo '</div>';
        }
    }
}
