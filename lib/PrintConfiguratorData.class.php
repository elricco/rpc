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

    private function getTemplatesById($id)
    {
        $templates = [];
        if (null != $id) {
            $ids = explode(',', $id);
            foreach ($ids as $template) {
                $sql = rex_sql::factory();
                $query = "SELECT rex_rpc_fixation_templates.*, group_concat(rex_rpc_template_colors.color_name ORDER BY rex_rpc_template_colors.id) color_names
                          FROM rex_rpc_fixation_templates
                          LEFT JOIN rex_rpc_template_colors
                          ON find_in_set(rex_rpc_template_colors.id, rex_rpc_fixation_templates.template_colors) > 0
                          WHERE rex_rpc_fixation_templates.id = ".$template;
                $rows = $sql->getArray($query);
                foreach ($rows as $cover) {
                    $templates['template_'.$cover['id']] = [
                        'id' => $cover['id'],
                        'name' => $cover['template_name'],
                        'description' => $cover['template_description'],
                        'image' => $cover['template_image'],
                        'colors' => $cover['color_names'],
                    ];
                }
            }
        }

        return $templates;
    }

    /**
     * @return array
     *
     * @throws rex_sql_exception
     */
    public function getFixationsAdditionsOptions()
    {
        $options = [];

        $sql2 = rex_sql::factory();
        $query2 = 'SELECT * FROM rex_rpc_fixation_addition_options';
        $resultVariation = $sql2->getArray($query2);
        foreach ($resultVariation as $variation) {
            $options[$variation['id']] = [
                'id' => $variation['id'],
                'name' => $variation['fixation_addition_option_name'],
                'description' => $variation['fixation_addition_options_description'],
                'price' => $variation['fixation_addition_options_price'],
            ];
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
        $basics = self::getBasics();
        $formatted_basics = [];
        $dom_elements = [];
        $data_check = [];
        $data_check_radio_options = '';
        $count = 0;

        foreach ($basics as $key => $basic) {
            if ('data_check_charge' == $basic['price_type'] && 0 == $count) {
                $data_check['data_check_default'] = $basic['id'];
                ++$count;
            }
        }

        // @ToDo: Get the already set defaults

        foreach ($basics as $key => $basic) {
            if ('order_flat_charge' == $basic['price_type']) {
                $dom_elements['order_flat_charge'] = '<div class="order-flat-charge border-bottom"><div class="row py-1"><div class="col">'.$basic['price_name'].'</div> <div class="col text-right" id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</div></div></div>';
                $formatted_basics['order_flat_charge'] = [
                    'id' => $basic['id'],
                    'label' => $basic['price_name'],
                    'price' => $basic['price_rate'],
                ];
            } elseif ('setup_flat_charge' == $basic['price_type']) {
                $dom_elements['setup_flat_charge'] = '<div class="setup_flat_charge"><span>'.$basic['price_name'].'</span> <span id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</span></div>';
            } elseif ('page_baw' == $basic['price_type'] || 'page_clr' == $basic['price_type']) {
                $dom_elements['sidebar_price'] .= '<span>'.$basic['price_name'].'</span> <span id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</span>';
                $formatted_basics['page_prices'][$basic['price_type']] = [
                    'type' => $basic['price_type'],
                    'name' => $basic['price_name'],
                    'price' => $basic['price_rate'],
                    'vat' => $basic['vat_rate'],
                    'min' => $basic['pages_min'],
                    'max' => $basic['pages_max'],
                    'start' => (!empty($session['amount_'.$basic['price_type']]) ? $session['amount_'.$basic['price_type']] : $basic['pages_min']),
                ];
            } elseif ('fixation_amount' == $basic['price_type']) {
                $dom_elements['sidebar_price'] .= '<span>'.$basic['price_name'].'</span> <span id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</span>';
                $formatted_basics['fixation_amount'] = [
                    'type' => $basic['price_type'],
                    'name' => $basic['price_name'],
                    'min' => $basic['pages_min'],
                    'max' => $basic['pages_max'],
                ];
            } elseif ('data_check_charge' == $basic['price_type']) {
                //$sidebar_price .= '<span>'.$basic['price_name'].'</span> <span id="'.$basic['price_type'].'" data-price="'.$basic['price_rate'].'">'.rex_formatter::number($basic['price_rate']).' '.$currency_symbol.'</span>';
                if (!isset($data_check['data_check_default']) && empty($data_check['data_check_default'])) {
                    $data_check['data_check_default'] = $basic['id'];
                }
                $data_check_radio_options .= '"'.$basic['price_name'].'":"'.$basic['id'].'"';
                if ($key < (count($basics) - 1)) {
                    $data_check_radio_options .= ',';
                }
                $data_check['formatted'][$basic['id']] = [
                    'id' => $basic['id'],
                    'price' => $basic['price_rate'],
                    'vat' => $basic['vat_rate'],
                    'label' => $basic['price_name'],
                ];
            }
            $data_check['data_check_json'] = $data_check_radio_options;
        }

        $data = [
            'basics' => $basics,
            'formatted_basics' => $formatted_basics,
            'dom_elements' => $dom_elements,
            'papers' => self::format_papers(),
            'data_check' => $data_check,
            'fixations' => self::format_fixations(),
            'fixation_additions' => self::format_fixationsAdditions(),
            'fixation_options' => self::buildVariation(),
        ];

        return $data;
    }

    protected function format_papers()
    {
        // Get Papers
        try {
            $papers = $this->getPapers();
        } catch (rex_sql_exception $e) {
            $papers = 'Couldn\'t get papers: '.$e;
        }

        $paper_radio_attributes = [];
        $paper_radio_attributes['unformatted'] = [$papers];
        $paper_default = $papers['0']['0']['id'];
        $paper_radio_options = '';
        // Define output of papers (for later)
        foreach ($papers as $key => $paper) {
            if (!isset($paper_default) && empty($paper_default)) {
                $paper_default = $paper['id'];
            }
            $paper_radio_options = $paper['paper_name'].'<br><small>'.str_replace(array(
                    ',',
                    '<p>',
                    '</p>',
                ), array('.', '', ''), $paper['paper_description']).'</small>';
            $paper_radio_attributes['formatted'][$paper['id']] = [
                'id' => $paper['id'],
                'price' => $paper['paper_price'],
                'vat' => $paper['vat_rate'],
                'fixations' => $paper['paper_fixation'],
                'strength' => $paper['paper_strength'],
                'label' => $paper_radio_options,
            ];
        }

        //build json
        $paper_json = '';
        foreach ($papers as $key => $paper) {
            $label = $paper['paper_name'].'<br><small>'.str_replace(array(
                    ',',
                    '<p>',
                    '</p>',
                ), array('.', '', ''), $paper['paper_description']).'</small>';
            $paper_json .= '"'.$label.'":"'.$paper['id'].'"';
            if ($key < (count($papers) - 1)) {
                $paper_json .= ',';
            }
        }

        $paper_radio_attributes['paper_default'] = $paper_default;
        $paper_radio_attributes['paper_json'] = $paper_json;

        $data = $paper_radio_attributes;

        return $data;
    }

    protected function format_fixations()
    {
        // Get Papers
        try {
            $fixations = $this->getFixations();
        } catch (rex_sql_exception $e) {
            $fixations = 'Couldn\'t get fixations: '.$e;
        }

        $fixation_radio_attributes = [];
        $fixation_radio_attributes['unformatted'] = $fixations;

        // Define output of fixations (for later)
        foreach ($fixations as $key => $fixation) {
            if (!isset($fixation_default) && empty($fixation_default)) {
                $fixation_default = $fixation['id'];
            }
            $fixation_radio_options .= $fixation['fixation_name'].'<br><small>'.str_replace(array(
            ',',
            '<p>',
            '</p>',
        ), array('.', '', ''), $fixation['fixation_description']).'</small>='.$fixation['id'];
            if ($key < (count($fixations) - 1)) {
                $fixation_radio_options .= ',';
            }
            $fixation_radio_attributes['formatted']['fixation_'.$fixation['id']] = [
                'id' => $fixation['id'],
                'type' => $fixation['fixation_type'],
                'name' => $fixation['fixation_name'],
                'price' => $fixation['fixation_price'],
                'vat' => $fixation['vat_rate'],
                'min' => $fixation['min'],
                'max' => $fixation['max'],
                'templates' => self::getTemplatesById($fixation['fixation_templates']),
            ];
        }

        $data = $fixation_radio_attributes;

        return $data;
    }

    protected function format_fixationsAdditions()
    {
        // Get Fixation Additions
        try {
            $fixationsAdditions = $this->getFixationsAdditions();
        } catch (rex_sql_exception $e) {
            $fixationsAdditions = 'Couldn\'t get fixations: '.$e;
        }

        $fixation_addition_radio_attributes = [];
        $fixation_addition_radio_attributes['unformatted'] = $fixationsAdditions;

        // Define output of fixations (for later)
        foreach ($fixationsAdditions as $key => $addition) {
            $fixation_addition_radio_attributes['formatted']['fixation_addition_'.$addition['id']] = [
                'id' => $addition['id'],
                'name' => $addition['name'],
                'price' => $addition['price'],
            ];
        }

        return $fixation_addition_radio_attributes;
    }

    public function buildVariation()
    {

        // Get Fixation Additions
        try {
            $fixationsAdditionsOptions = $this->getFixationsAdditionsOptions();
        } catch (rex_sql_exception $e) {
            $fixationsAdditionsOptions = 'Couldn\'t get fixations: '.$e;
        }

        $variations = [];
        $variations['unformatted'] = [$fixationsAdditionsOptions];
        $variations_default = $fixationsAdditionsOptions['1']['id'];

        $variations_json = '';
        foreach ($fixationsAdditionsOptions as $key => $variation) {
            if (!isset($variations_default) && empty($variations_default)) {
                $variations_default = $variation['id'];
            }
            $variations['formatted'][$variation['id']] = [
                'id' => $variation['id'],
                'name' => $variation['name'],
                'description' => $variation['description'],
                'price' => $variation['price'],
            ];
            $variations_json .= '"'.$variation['name'].'":"'.$variation['id'].'"';
            if ($key < (count($variations))) {
                $variations_json .= ',';
            }
        }

        $variations['variations_default'] = $variations_default;
        $variations['variations_json'] = $variations_json;

        $data = $variations;

        return $data;
    }

    public function getVariationDefault($elements = [])
    {
        foreach ($elements as $key => $variation) {
            if (!isset($variation_default) && empty($variation_default)) {
                $variation_default = $variation['id'];
            }
        }

        return $variation_default;
    }

    public function getData()
    {
        try {
            $basics = $this->format_basics();
        } catch (rex_sql_exception $e) {
            $basics = 'Couldn\'t get basics: '.$e;
        }

        return $basics;
    }
}
