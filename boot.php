<?php

rex_extension::register('PACKAGES_INCLUDED', function () {
    // Register YForm templates
    if (rex_addon::get('yform')->isAvailable()) {
        rex_yform::addTemplatePath(rex_path::addon('rpc', 'ytemplates'));
    }
}, rex_extension::LATE);
