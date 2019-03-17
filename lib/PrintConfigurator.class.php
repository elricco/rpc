<?php
/**
 * Class PrintConfigurator.
 */
class PrintConfigurator
{
    protected $total_price = 0;
    protected $page_price_baw = 0;
    protected $page_price_clr = 0;

    protected $page_name_baw = '';
    protected $page_name_clr = '';

    protected $total_pages = 0;
    protected $one_or_double_sided = 0;

    protected $data_check = 0;
    protected $data_check_price = 0;

    protected $paper_option = 0;
    protected $paper_option_price = 0;

    /**
     * @param $reference
     * @param $amount
     * @param $price
     * @param $currency
     * @param $label
     *
     * @return string
     */
    private function generateSidebarDom($reference, $amount, $price, $label = '', $currency = '€')
    {
        return '<div id="order-'.$reference.'" class="border-bottom">'.
               '    <div class="row py-1">'.
               '        <div class="col-6">'.$label.'</div>'.
               '        <div class="col-2"><small>x'.$amount.'</small></div>'.
               '        <div class="col-4 text-right">'.number_format($price, 2, ',', '.').' '.$currency.'</div>'.
               '    </div>'.
               '</div>';
    }

    /**
     * @param $number
     *
     * @return int
     */
    public function isOdd($number)
    {
        return $number % 2;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function calculate_price_for_dom($data)
    {
        $prices = [];
        $rpc_data = new PrintConfiguratorData();
        $basics = $rpc_data->getData();

        //substract colored pages from total pages
        //maybe add a switch to config page if colored pages should be substracted?
        //$baw_pages = intval($data['page_baw']) - intval($data['page_clr']);
        $baw_pages = intval($data['page_baw']);
        $this->total_pages = intval($data['page_baw']);

        $this->page_price_baw = number_format(intval($baw_pages) * floatval($basics['formatted_basics']['page_prices']['page_baw']['price']), 2);
        $this->page_price_clr = number_format(intval($data['page_clr']) * floatval($basics['formatted_basics']['page_prices']['page_clr']['price']), 2);

        $this->total_price = number_format(floatval($this->page_price_baw) + floatval($this->page_price_clr), 2);

        //calculate data check price
        $this->data_check = $data['data_check'];
        $this->data_check_price = $basics['data_check']['formatted'][$this->data_check]['price'];

        $this->total_price = number_format(floatval($this->total_price) + floatval($this->data_check_price), 2);

        //calculate paper options price
        $this->paper_option = $data['paper_options'];
        $this->paper_option_price = number_format(intval($this->total_pages) * floatval($basics['papers']['formatted'][$this->paper_option]['price']), 2);

        $this->total_price = number_format(floatval($this->total_price) + floatval($this->paper_option_price), 2);

        //model output
        $prices['prices'] = [
            'page_baw_price' => $this->page_price_baw,
            'page_clr_price' => $this->page_price_clr,
            'data_check_price' => $this->data_check_price,
            'paper_price' => $this->paper_option_price,
            'total_price' => $this->total_price,
        ];

        //re-calculate prices if double-sided prints is checked
        $this->one_or_double_sided = $data['one_or_double-sided'];

        if (1 == $this->one_or_double_sided) {
            $this->total_pages = round(round($this->total_pages / 2) * 2) / 2;
            if (1 == $this->isOdd($this->total_pages)) {
                ++$this->total_pages;
            }
            $this->page_price_baw = number_format(intval($this->total_pages) * floatval($basics['formatted_basics']['page_prices']['page_baw']['price']), 2);
            $this->total_price = number_format(floatval($this->total_price) - floatval($prices['prices']['page_baw_price']), 2);
            $prices['prices']['page_baw_price'] = $this->page_price_baw;

            $this->total_price = number_format(floatval($this->total_price) + floatval($this->page_price_baw), 2);
            $prices['prices']['total_price'] = $this->total_price;
        }

        // dom output needs to be modeled latest
        $this->page_name_baw = $basics['formatted_basics']['page_prices']['page_baw']['name'];
        $this->page_name_clr = $basics['formatted_basics']['page_prices']['page_clr']['name'];

        //model output
        $prices['dom_elements'] = [
            'order-paper' => $this->generateSidebarDom('paper_baw', $data['page_baw'], $this->page_price_baw, $this->page_name_baw).$this->generateSidebarDom('paper_clr', $data['page_clr'], $this->page_price_clr, $this->page_name_clr),
        ];

        // @ToDo: Remove this before release
        //get REDAXO config file
        $configFile = rex_path::coreData('config.yml');
        $config = rex_file::getConfig($configFile);

        //when debug is set and true, include function(s)
        if (isset($config['debug']['enabled']) && true === $config['debug']['enabled']) {
            $prices['GET'] = [
                'data' => $data,
            ];
        }
        // @ToDo: Remove this before release - END

        return $prices;
    }

    /**
     * @param $order
     *
     * @return array
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
