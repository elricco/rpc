<?php

if (empty('REX_LINK[1]')) {
    if (rex::isBackend()) {
        echo 'Bitte den Link zur Seite mit dem Konfigurator setzen!';
    }
} elseif (empty('REX_LINK[2]')) {
    if (rex::isBackend()) {
        echo 'Bitte den Link zur Seite mit den Rechnungsdaten setzen!';
    }
} else {
    //check if session has value order - if not redirect to print configurator
    if (!isset($_SESSION) && !rex::isBackend()) {
        rex_redirect('REX_LINK[1]');
    } else {
        if (empty(rex_session('order', 'array', ''))) {
            rex_redirect('REX_LINK[1]');
        } else {
            $order_config = rex_session('order', 'array', '');
            $rpcData = new PrintConfiguratorData();
            $data = $rpcData->getData();

            //debug stuff
            dump($order_config);
            dump($data);

            if ($order_config['FIXATION_SPECIAL']) {
                $title_options = 'Daten Titelseite & CD/DVD';
            } else {
                $title_options = 'Daten CD/DVD';
            }

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
            echo '<h1>Optionen</h1>';
            echo '</div></div></div>';
            echo '<div class="container">';

            $yform = new rex_yform();
            $yform->setDebug(true);
            $yform->setObjectparams('real_field_names', 1);
            $yform->setObjectparams('article_id', rex_article::getCurrentId());
            $yform->setRedaxoVars(REX_ARTICLE_ID);
            $yform->setActionField('callback', array('PrintConfigurator::setOptions'));
            $yform->setActionField('redirect', array('REX_LINK[2]'));
            $yform->setObjectparams('form_name', 'options_form');

            // open left area
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-8">'));

            // data area
            $yform->setValueField('fieldset', array('name_and_number', $title_options));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('firstname', 'Vorname',  '',  '',  '{"placeholder":"Vorname","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
            $yform->setValueField('text', array('lastname', 'Nachname',  '',  '',  '{"placeholder":"Nachname","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-9">'));
            $yform->setValueField('text', array('student_number', 'Matrikelnummer',  '',  '',  '{"placeholder":"Matrikelnummer","required":"required"}'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-3">'));
            $yform->setValueField('date', array('date', 'Jahr', '2000', '+25', 'YYYY', '1', '', 'select'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('html', array('html', 'HTML', '</div>'));
            $yform->setValueField('text', array('type_of_work', 'Art',  '',  '',  '{"placeholder":"Art","required":"required"}'));
            $yform->setValueField('text', array('title', 'Titel',  '',  '',  '{"placeholder":"Titel","required":"required"}'));

            if ($order_config['FIXATION_SPECIAL']) {
                $yform->setValueField('fieldset', array('spine_data', 'Daten Buchrücken'));
                $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
                $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
                $yform->setValueField('text', array('spine_firstname',  'Vorname',  '',  '',  '{"placeholder":"Vorname","required":"required"}'));
                $yform->setValueField('html', array('html', 'HTML', '</div>'));
                $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
                $yform->setValueField('text', array('spine_lastname', 'Nachname',  '',  '',  '{"placeholder":"Nachname","required":"required"}'));
                $yform->setValueField('html', array('html', 'HTML', '</div>'));
                $yform->setValueField('html', array('html', 'HTML', '</div>'));
                $yform->setValueField('text', array('spine_type_of_work', 'Art',  '',  '',  '{"placeholder":"Art","required":"required"}'));
                $yform->setValueField('text', array('spine_title', 'Titel',  '',  '',  '{"placeholder":"Titel","required":"required"}'));
            }

            foreach ($order_config['item_collection']['fixations'] as $fixation_template) {
                if (!empty($fixation_template['templates'])) {
                    $yform->setValueField('fieldset', array('fixation_template_'.$fixation_template['id'], $fixation_template['label']));
                    $yform->setValueField('html', array('html', 'HTML', '<div class="row">'));
                    $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));

                    $template = '';
                    $colors = '';
                    $color_change = '';
                    $i = 0;
                    foreach ($fixation_template['templates'] as $key => $fx_template) {
                        $template .= '"'.$fx_template['name'].'":"template_'.$fx_template['id'].'"';
                        if (0 == $i) {
                            $colors = $fx_template['colors'];
                            $color_change .= '<span id="template_'.$fx_template['id'].'" data-template-colors="'.$fx_template['colors'].'"></span>';
                        } else {
                            $color_change .= '<span id="template_'.$fx_template['id'].'" data-template-colors="'.$fx_template['colors'].'"></span>';
                        }

                        if ($i < (count($fixation_template['templates']) - 1)) {
                            $template .= ',';
                            ++$i;
                        }
                    }

                    $yform->setValueField('choice', ['fixation_'.$fixation_template['id'].'_template', 'Template', '{'.$template.'}', 0, 0, '0']);

                    $yform->setValueField('html', array('html', 'HTML', '</div>'));
                    $yform->setValueField('html', array('html', 'HTML', '<div class="col-12 col-md-6">'));
                    $yform->setValueField('html', array('html', 'HTML', $color_change));
                    $yform->setValueField('choice', ['fixation_'.$fixation_template['id'].'_template_color', 'Farbe', $colors, 0, 0, '0']);
                    $yform->setValueField('html', array('html', 'HTML', '</div>'));
                    $yform->setValueField('html', array('html', 'HTML', '</div>'));
                }
            }

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
