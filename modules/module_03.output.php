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

            dump($order_config);
            dump($options_config);

            echo 'Blubb';
        }
    }
}
