<?php

//delete tables from yform
rex_yform_manager_table_api::removeTable('rex_rpc_vat_types');
rex_yform_manager_table_api::removeTable('rex_rpc_general_prices');
rex_yform_manager_table_api::removeTable('rex_rpc_paper_option');
rex_yform_manager_table_api::removeTable('rex_rpc_fixation_option');
rex_yform_manager_table_api::removeTable('rex_rpc_fixation_addition');
rex_yform_manager_table_api::removeTable('rex_rpc_fixation_addition_options');

//delete tables from database
rex_sql_table::get(rex::getTable('rpc_vat_types'))->drop();
rex_sql_table::get(rex::getTable('rpc_general_prices'))->drop();
rex_sql_table::get(rex::getTable('rpc_paper_option'))->drop();
rex_sql_table::get(rex::getTable('rpc_fixation_option'))->drop();
rex_sql_table::get(rex::getTable('rpc_fixation_addition'))->drop();
rex_sql_table::get(rex::getTable('rpc_fixation_addition_options'))->drop();
