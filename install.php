<?php
/** @var rex_addon $this */
$vat_types = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_vat_types.json'));
rex_yform_manager_table_api::importTablesets($vat_types);

$general_prices = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_general_prices.json'));
rex_yform_manager_table_api::importTablesets($general_prices);

$paper_option = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_paper_option.json'));
rex_yform_manager_table_api::importTablesets($paper_option);

$fixation_option = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_fixation_option.json'));
rex_yform_manager_table_api::importTablesets($fixation_option);

$fixation_addition = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_fixation_addition.json'));
rex_yform_manager_table_api::importTablesets($fixation_addition);

$fixation_addition_options = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_fixation_addition_options.json'));
rex_yform_manager_table_api::importTablesets($fixation_addition_options);


