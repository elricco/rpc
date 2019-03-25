<?php
/**
 * Class PrintConfigurator.
 */
class PrintConfigurator
{
    protected $total_price = 0;
    protected $order_flat_charge = 0;
    protected $page_price_baw = 0;
    protected $page_price_clr = 0;
    protected $page_order_price = 0;

    protected $page_name_baw = '';
    protected $page_name_clr = '';

    protected $total_pages = 0;
    protected $one_or_double_sided = 0;

    protected $data_check = 0;
    protected $data_check_price = 0;

    protected $paper_option = 0;
    protected $paper_option_price = 0;

    protected $fixations = [];
    protected $fixations_total_price = 0;
    protected $fixations_total_amount = 1;

    protected $fixation_additions = [];
    protected $fixation_additions_total_price = 0;
    protected $fixation_additions_total_amount = 0;

    protected $fixation_additions_option = 0;
    protected $fixation_additions_option_total_price = 0;
    protected $fixation_additions_option_total_amount = 0;

    /**
     * @param $reference
     * @param $amount
     * @param $price
     * @param string $label
     * @param string $currency
     * @param bool   $withborder
     *
     * @return string
     */
    private function generateSidebarDom($reference, $amount, $price, $label = '', $currency = 'EUR', $withborder = true)
    {
        return '<div id="order-'.$reference.'" '.(true == $withborder ? 'class="border-bottom"' : '').'>'.
               '    <div class="row py-1">'.
               '        <div class="col-6">'.$label.'</div>'.
               '        <div class="col-2"><small>x'.$amount.'</small></div>'.
               '        <div class="col-4 text-right">'.number_format($price, 2, ',', '.').' '.$currency.'</div>'.
               '    </div>'.
               '</div>';
    }

    /**
     * @param $reference
     * @param $price
     * @param string $label
     * @param string $currency
     * @param bool   $withborder
     *
     * @return string
     */
    private function generateSidebarDomLight($reference, $price, $label = '', $currency = 'EUR', $withborder = true)
    {
        return '<div id="order-'.$reference.'" '.(true == $withborder ? 'class="border-bottom"' : '').'>'.
               '    <div class="row py-1">'.
               '        <div class="col">'.$label.'</div>'.
               '        <div class="col text-right">'.number_format($price, 2, ',', '.').' '.$currency.'</div>'.
               '    </div>'.
               '</div>';
    }

    /**
     * @param array $postData
     * @param array $formattedValues
     * @param array $internalReference - needs to be a value of internal var like $this->fixations
     * @param $totalPrice - needs to be a value of internal var like $this->fixations_total_price
     * @param bool $fixationsTotalAmount
     *
     * @return float
     */
    private function calculateInputFieldsOfSameKind(array $postData, array $formattedValues, array &$internalReference, &$totalPrice, &$totalAmount)
    {
        //calculate single prices and push to array
        foreach ($formattedValues as $key => $value) {
            if (0 != $postData[$key]) {
                $totalAmount = 0;
                $price = intval($postData[$key]) * floatval($value['price']);
                $internalReference[$key] = [
                    'id' => $value['id'],
                    'price' => $price,
                    'amount' => $postData[$key],
                    'label' => $value['name'],
                ];
            }
        }

        //calculate total price and amount
        foreach ($internalReference as $key => $value) {
            $totalPrice = floatval($totalPrice) + floatval($value['price']);
            $totalAmount = intval($totalAmount) + intval($value['amount']);
        }

        return $totalPrice;
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

        $this->order_flat_charge = floatval($basics['formatted_basics']['order_flat_charge']['price']);

        //model output
        $prices['prices'] = [
            'order_flat_charge' => $this->order_flat_charge,
            'page_baw_price' => $this->page_price_baw,
            'page_clr_price' => $this->page_price_clr,
            'page_order_price' => $this->page_order_price,
            'data_check_price' => $this->data_check_price,
            'paper_price' => $this->paper_option_price,
            'fixations_total_price' => $this->fixations_total_price,
            'fixation_additions_total_price' => $this->fixation_additions_total_price,
            'fixation_additions_option_total_price' => $this->fixation_additions_option_total_price,
            'total_price' => $this->total_price,
        ];

        //substract colored pages from total pages
        //maybe add a switch to config page if colored pages should be substracted?
        //$baw_pages = intval($data['page_baw']) - intval($data['page_clr']);
        $baw_pages = intval($data['page_baw']);
        $this->total_pages = intval($data['page_baw']);

        //calculate total / baw pages
        $this->page_price_baw = intval($baw_pages) * floatval($basics['formatted_basics']['page_prices']['page_baw']['price']);
        $prices['prices']['page_baw_price'] = $this->page_price_baw;

        //calculate baw pages with order_charge
        $this->page_order_price = floatval($this->page_price_baw) + floatval($basics['formatted_basics']['order_flat_charge']['price']);
        $prices['prices']['page_order_price'] = $this->page_order_price;

        //calculate coloured pages
        $this->page_price_clr = intval($data['page_clr']) * floatval($basics['formatted_basics']['page_prices']['page_clr']['price']);
        $prices['prices']['page_clr_price'] = $this->page_price_clr;

        //calculate data check price
        $this->data_check = $data['data_check'];
        $this->data_check_price = floatval($basics['data_check']['formatted'][$this->data_check]['price']);
        $prices['prices']['data_check_price'] = $this->data_check_price;

        //calculate paper options price
        $this->paper_option = $data['paper_options'];
        $this->paper_option_price = intval($this->total_pages) * floatval($basics['papers']['formatted'][$this->paper_option]['price']);
        $prices['prices']['paper_price'] = $this->paper_option_price;

        //calculate fixations single prices and push to array
        $prices['prices']['fixations_total_price'] = self::calculateInputFieldsOfSameKind($data, $basics['fixations']['formatted'], $this->fixations, $this->fixations_total_price, $this->fixations_total_amount);

        //calculate fixation additions single prices and push to array
        $prices['prices']['fixation_additions_total_price'] = self::calculateInputFieldsOfSameKind($data, $basics['fixation_additions']['formatted'], $this->fixation_additions, $this->fixation_additions_total_price, $this->fixation_additions_total_amount);

        //calculate fixation additions optios single prices and push to array
        $this->fixation_additions_option = $data['fixation_options'];
        $this->fixation_additions_option_total_amount = intval($this->fixation_additions_total_amount);
        $this->fixation_additions_option_total_price = intval($this->fixation_additions_option_total_amount) * floatval($basics['fixation_options']['formatted'][$this->fixation_additions_option]['price']);
        $prices['prices']['fixation_additions_option_total_price'] = $this->fixation_additions_option_total_price;

        //calculate total pages and paper option price
        $this->total_pages = intval($this->total_pages) * intval($this->fixations_total_amount);
        $this->paper_option_price = intval($this->total_pages) * floatval($basics['papers']['formatted'][$this->paper_option]['price']);
        $prices['prices']['paper_price'] = $this->paper_option_price;

        //re-calculate prices if double-sided prints is checked
        $this->one_or_double_sided = $data['one_or_double-sided'];

        if (1 == $this->one_or_double_sided) {
            $this->total_pages = round(round($this->total_pages / 2) * 2) / 2;
            if (1 == $this->isOdd($this->total_pages)) {
                ++$this->total_pages;
            }
            //re-calculate paper option price
            $this->paper_option_price = intval($this->total_pages) * floatval($basics['papers']['formatted'][$this->paper_option]['price']);
            $prices['prices']['paper_price'] = $this->paper_option_price;
        }

        $this->total_price =
            floatval($this->order_flat_charge) +
            floatval($this->page_price_baw) +
            floatval($this->page_price_clr) +
            floatval($this->data_check_price) +
            floatval($this->paper_option_price) +
            floatval($this->fixation_additions_total_price) +
            floatval($this->fixation_additions_option_total_price) +
            floatval($this->fixations_total_price);
        $prices['prices']['total_price'] = round($this->total_price, 2);

        // dom output needs to be modeled latest
        $this->page_name_baw = $basics['formatted_basics']['page_prices']['page_baw']['name'];
        $this->page_name_clr = $basics['formatted_basics']['page_prices']['page_clr']['name'];

        //fixations dom output
        $fixations_dom = '';
        foreach ($this->fixations as $key => $fixation) {
            $fixations_dom .= $this->generateSidebarDom($key, $data[$key], $fixation['price'], $fixation['label']);
        }

        foreach ($this->fixation_additions as $key => $addition) {
            $fixations_dom .= $this->generateSidebarDom($key, $data[$key], $addition['price'], $addition['label']);
        }
        $fixations_dom .= $this->generateSidebarDom('fixation_options', $this->fixation_additions_option_total_amount, $this->fixation_additions_option_total_price, $basics['fixation_options']['formatted'][$this->fixation_additions_option]['name']);

        //model output
        $prices['dom_elements'] = [
            'order-data_check' => self::generateSidebarDomLight('data_check', $this->data_check_price, $basics['data_check']['formatted'][$data['data_check']]['label'], 'EUR', false),
            'order-paper' => $this->generateSidebarDom('paper_baw', $data['page_baw'], $this->page_order_price, $this->page_name_baw).
                             $this->generateSidebarDom('paper_clr', $data['page_clr'], $this->page_price_clr, $this->page_name_clr).
                             $this->generateSidebarDom('paper', $this->total_pages, $this->paper_option_price, $basics['papers']['formatted'][$data['paper_options']]['label'], 'EUR', false),
            'order-subtotal' => self::generateSidebarDomLight('subtotal', $this->total_price, 'Zwischensumme', 'EUR', false),
            'order-fixations' => $fixations_dom,
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
