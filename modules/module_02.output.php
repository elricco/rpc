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
            dump($session);
            echo 'REX_LINK[1]';
        }
    }
}
