<?php

class PrintConfiguratorData
{
    /**
     * @return array
     *
     * @throws rex_sql_exception
     */
    private function getBasics()
    {
        $sql = rex_sql::factory();
        $query = 'SELECT rex_rpc_general_prices.*, rex_rpc_vat_types.vat_description, rex_rpc_vat_types.vat_rate 
                  FROM rex_rpc_general_prices 
                  LEFT JOIN rex_rpc_vat_types 
                  ON rex_rpc_general_prices.price_vat = rex_rpc_vat_types.id';
        $rows = $sql->getArray($query);

        return $rows;
    }

    /**
     * @return array
     *
     * @throws rex_sql_exception
     */
    private function getPapers()
    {
        $sql = rex_sql::factory();
        $query = 'SELECT rex_rpc_paper_option.*, rex_rpc_vat_types.vat_description, rex_rpc_vat_types.vat_rate 
                  FROM rex_rpc_paper_option 
                  LEFT JOIN rex_rpc_vat_types 
                  ON rex_rpc_paper_option.paper_vat = rex_rpc_vat_types.id';
        $rows = $sql->getArray($query);

        return $rows;
    }

    /**
     * @param $id
     *
     * @return array
     *
     * @throws rex_sql_exception
     */
    private function getPapersById($id)
    {
        $sql = rex_sql::factory();
        $query = 'SELECT rex_rpc_paper_option.*, rex_rpc_vat_types.vat_description, rex_rpc_vat_types.vat_rate 
                  FROM rex_rpc_paper_option 
                  LEFT JOIN rex_rpc_vat_types 
                  ON rex_rpc_paper_option.paper_vat = rex_rpc_vat_types.id 
                  WHERE rex_rpc_paper_option.id = '.$id;
        $rows = $sql->getArray($query);

        return $rows;
    }

    /**
     * @return array
     *
     * @throws rex_sql_exception
     */
    private function getFixations()
    {
        $sql = rex_sql::factory();
        $query = 'SELECT rex_rpc_fixation_option.*, rex_rpc_vat_types.vat_description, rex_rpc_vat_types.vat_rate 
                  FROM rex_rpc_fixation_option 
                  LEFT JOIN rex_rpc_vat_types 
                  ON rex_rpc_fixation_option.fixation_vat = rex_rpc_vat_types.id';
        $rows = $sql->getArray($query);

        return $rows;
    }

    /**
     * @return array
     *
     * @throws rex_sql_exception
     */
    private function getFixationsAdditions()
    {
        $options = [];

        $sql = rex_sql::factory();
        $query = 'SELECT * FROM rex_rpc_fixation_addition';
        $rows = $sql->getArray($query);
        foreach ($rows as $result) {
            $options[$result['id']] = [
                'id' => $result['id'],
                'name' => $result['name'],
                'description' => $result['description'],
                'price' => $result['price'],
            ];
        }

        foreach ($rows as $option) {
            $sql2 = rex_sql::factory();
            $query2 = 'SELECT * FROM rex_rpc_fixation_addition_options 
                       WHERE fixation_addition_options_relation = '.$option['id'];
            $resultVariation = $sql2->getArray($query2);
            foreach ($resultVariation as $variation) {
                $options[$option['id']]['variations'][$variation['id']] = [
                    'id' => $variation['id'],
                    'name' => $variation['fixation_addition_option_name'],
                    'description' => $variation['fixation_addition_options_description'],
                    'price' => $variation['fixation_addition_options_price'],
                ];
            }
        }

        return $options;
    }

    /**
     * @return array
     *
     * @throws rex_sql_exception
     */
    protected function format_basics()
    {
        $basics = $this->getBasics();
        foreach ($basics as $key => $basic) {
            if ('order_flat_charge' == $basic['price_type']) {
                $order_flat_charge .= '<div class="order-flat-charge border-bottom"><div class="row py-1"><div class="col">'.$basic['price_name'].'</div> <div class="col text-right" id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</div></div></div>';
            } elseif ('setup_flat_charge' == $basic['price_type']) {
                $setup_flat_charge .= '<div class="setup_flat_charge"><span>'.$basic['price_name'].'</span> <span id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</span></div>';
            } elseif ('page_baw' == $basic['price_type'] || 'page_clr' == $basic['price_type']) {
                $sidebar_price .= '<span>'.$basic['price_name'].'</span> <span id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</span>';
                $page_prices[$basic['price_type']] = [
                    'type' => $basic['price_type'],
                    'name' => $basic['price_name'],
                    'price' => $basic['price_rate'],
                    'vat' => $basic['vat_rate'],
                    'min' => $basic['pages_min'],
                    'max' => $basic['pages_max'],
                    'start' => (!empty($session['amount_'.$basic['price_type']]) ? $session['amount_'.$basic['price_type']] : $basic['pages_min']),
                ];
            } elseif ('fixation_amount' == $basic['price_type']) {
                $sidebar_price .= '<span>'.$basic['price_name'].'</span> <span id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</span>';
                $fixation_amount = [
                    'type' => $basic['price_type'],
                    'name' => $basic['price_name'],
                    'min' => $basic['pages_min'],
                    'max' => $basic['pages_max'],
                ];
            } elseif ('data_check_charge' == $basic['price_type']) {
                //$sidebar_price .= '<span>'.$basic['price_name'].'</span> <span id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</span>';
                if (!isset($data_check_default) && empty($data_check_default)) {
                    $data_check_default = $basic['id'];
                }
                $data_check_radio_options .= $basic['price_name'].'='.$basic['id'];
                if ($key < (count($basics) - 1)) {
                    $data_check_radio_options .= ',';
                }
                $data_check_radio_attributes[$basic['id']] = [
                    'price' => $basic['price_rate'],
                    'vat' => $basic['vat_rate'],
                ];
            }
        }

        return $basics;
    }

    public function getData() {
        $basics = $this->format_basics();
        return $basics;
    }
}
