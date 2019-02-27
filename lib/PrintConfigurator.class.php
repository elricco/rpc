<?php
/**
 * Class PrintConfigurator.
 */
class PrintConfigurator
{
    /**
     * @return array
     *
     * @throws rex_sql_exception
     */
    public function getBasics()
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
    public function getPapers()
    {
        $sql = rex_sql::factory();
        $query = 'SELECT rex_rpc_paper_option.*, rex_rpc_vat_types.vat_description, rex_rpc_vat_types.vat_rate FROM rex_rpc_paper_option LEFT JOIN rex_rpc_vat_types ON rex_rpc_paper_option.paper_vat = rex_rpc_vat_types.id';
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
    public function getPapersById($id)
    {
        $sql = rex_sql::factory();
        $query = 'SELECT rex_rpc_paper_option.*, rex_rpc_vat_types.vat_description, rex_rpc_vat_types.vat_rate FROM rex_rpc_paper_option LEFT JOIN rex_rpc_vat_types ON rex_rpc_paper_option.paper_vat = rex_rpc_vat_types.id WHERE rex_rpc_paper_option.id = '.$id;
        $rows = $sql->getArray($query);

        return $rows;
    }

    /**
     * @return array
     *
     * @throws rex_sql_exception
     */
    public function getFixations()
    {
        $sql = rex_sql::factory();
        $query = 'SELECT rex_rpc_fixation_option.*, rex_rpc_vat_types.vat_description, rex_rpc_vat_types.vat_rate FROM rex_rpc_fixation_option LEFT JOIN rex_rpc_vat_types ON rex_rpc_fixation_option.fixation_vat = rex_rpc_vat_types.id';
        $rows = $sql->getArray($query);

        return $rows;
    }

    /**
     * @return array
     *
     * @throws rex_sql_exception
     */
    public function getFixationsAdditions()
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
            $query2 = 'SELECT * FROM rex_rpc_fixation_addition_options WHERE fixation_addition_options_relation = '.$option['id'];
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
     * @param $basics
     */
    public function format_basics($basics)
    {
        foreach ($basics as $key => $basic) {
            //dump($basic);
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
    }

    /**
     * @param $order
     *
     * @return array
     * @throws rex_sql_exception
     */
    public function calculate_price($order)
    {
        // Get Basics
        $basics = self::getBasics();
        foreach ($basics as $key => $basic) {
            if ('page_baw' == $basic['price_type']) {
                $pages['baw'] = [
                    'name' => $basic['price_name'],
                    'price' => $basic['price_rate'],
                    'vat' => $basic['vat_rate'],
                    'min' => $basic['pages_min'],
                    'max' => $basic['pages_max'],
                ];
            }
            if ('page_clr' == $basic['price_type']) {
                $pages['clr'] = [
                    'name' => $basic['price_name'],
                    'price' => $basic['price_rate'],
                    'vat' => $basic['vat_rate'],
                    'min' => $basic['pages_min'],
                    'max' => $basic['pages_max'],
                ];
            }
        }
        $total = 0;
        $subtotal = 0;
        //this needs to be a checkbox for easier handling here
        $secondFixation = 0;

        //calculate pages price
        $pages['baw']['amount'] = $order['amount_page_baw'];
        $pages['clr']['amount'] = $order['amount_page_clr'];
        $pages['total']['amount'] = $order['amount_page_total'];

        //single or double sided print?
        $double_sided = $order['double_sided'];

        //which paper?
        $paper = self::getPapersById($order['paper_options']);

        $amount_fixation_one = $order['amount_fixation'];
        $amount_fixation_two = $order['amount_fixation_two'];

        //calculate page numbers
        $totalPages = $pages['baw']['amount'] + $pages['clr']['amount'];
        // since it's 0 == false or 1 == true this should do the trick
        if ($double_sided) {
            $totalPages = (($totalPages / 2) * 2) / 2;
            //check if number is odd
            if (0 != $totalPages % 2) {
                ++$totalPages;
            }
        }
        $pages['total']['amount_single'] = $totalPages;

        // calculate book strength here because we have the right amount for one book
        $pages['total']['book_strength'] = round($pages['total']['amount_single'] * $paper[0]['paper_strength']);

        // multiply totalPages with fixation_amount
        $totalPagesOne = $pages['total']['amount_single'] * $amount_fixation_one;
        $totalPagesTwo = $pages['total']['amount_single'] * $amount_fixation_two;

        //shit I need to make that second fixation a checkbox
        if ($secondFixation) {
            $paper['total']['amount'] = $totalPagesOne + $totalPagesTwo;
        } else {
            $paper['total']['amount'] = $totalPagesOne;
        }

        $totalFixations = ($amount_fixation_one + ($secondFixation ? $amount_fixation_two : 0));

        $pages['total']['baw_price'] = ($pages['baw']['amount'] * $pages['baw']['price']) * $totalFixations;
        $pages['total']['clr_price'] = ($pages['clr']['amount'] * $pages['clr']['price']) * $totalFixations;
        $pages['total']['price'] = $pages['total']['baw_price'] + $pages['total']['clr_price'];

        $paper['total']['price'] = $paper['total']['amount'] * $paper[0]['paper_price'];

        $contact = [
            'invoice_data' => [
                'gender' => $order['gender'],
                'firstname' => $order['firstname'],
                'lastname' => $order['lastname'],
                'street' => $order['street'],
                'street_no' => $order['street_no'],
                'zip' => $order['zip'],
                'city' => $order['city'],
                'email' => $order['email'],
                'phone' => $order['phone'],
            ],
        ];

        $revisited_order = [
            'pages' => $pages,
            'paper' => $paper,
            'fixations' => $totalFixations,
            'contact' => $contact,
        ];

        return $revisited_order;
    }

    public function setAddress()
    {
        rex_set_session('address', $_POST);
    }

    public function setOrder()
    {
        //rex_set_session( 'order', serialize($_POST));
        rex_set_session('order', $_POST);
    }
}
