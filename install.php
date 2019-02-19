<?php
/** @var rex_addon $this */
$vat_types = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_vat_types.json'));
rex_yform_manager_table_api::importTablesets($vat_types);