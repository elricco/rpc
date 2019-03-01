<?php

/**
 * @return string
 *
 * @throws Exception
 */
function rpc_import_table_schemes()
{
    $message = '';

    if (1 != rex_sql_table::get(rex::getTable('rpc_vat_types'))->exists()) {
        $vat_types = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_vat_types.json'));
        try {
            rex_yform_manager_table_api::importTablesets($vat_types);
        } catch (Exception $e) {
            $message .= rex_view::error('Exception: '.$e->getMessage());
        }
    }
    if (1 != rex_sql_table::get(rex::getTable('rpc_general_prices'))->exists()) {
        $general_prices = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_general_prices.json'));
        try {
            rex_yform_manager_table_api::importTablesets($general_prices);
        } catch (Exception $e) {
            $message .= rex_view::error('Exception: '.$e->getMessage());
        }
    }

    if (1 != rex_sql_table::get(rex::getTable('rpc_paper_option'))->exists()) {
        $paper_option = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_paper_option.json'));
        try {
            rex_yform_manager_table_api::importTablesets($paper_option);
        } catch (Exception $e) {
            $message .= rex_view::error('Exception: '.$e->getMessage());
        }
    }
    if (1 != rex_sql_table::get(rex::getTable('rpc_fixation_option'))->exists()) {
        $fixation_option = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_fixation_option.json'));
        try {
            rex_yform_manager_table_api::importTablesets($fixation_option);
        } catch (Exception $e) {
            $message .= rex_view::error('Exception: '.$e->getMessage());
        }
    }
    if (1 != rex_sql_table::get(rex::getTable('rpc_fixation_addition'))->exists()) {
        $fixation_addition = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_fixation_addition.json'));
        try {
            rex_yform_manager_table_api::importTablesets($fixation_addition);
        } catch (Exception $e) {
            $message .= rex_view::error('Exception: '.$e->getMessage());
        }
    }

    if (1 != rex_sql_table::get(rex::getTable('rpc_fixation_addition_options'))->exists()) {
        $fixation_addition_options = rex_file::get(rex_path::addon('rpc', 'install/tablesets/rpc_fixation_addition_options.json'));
        try {
            rex_yform_manager_table_api::importTablesets($fixation_addition_options);
        } catch (Exception $e) {
            $message .= rex_view::error('Exception: '.$e->getMessage());
        }
    }

    return $message;
}

try {
    rpc_import_table_schemes();
} catch (Exception $e) {
}
