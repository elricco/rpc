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
            //dump($session);

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
            $yform->setValueField('fieldset', array('name_and_number', 'Daten Titelseite & CD/DVD'));
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
