<?php
/**
 * Class PrintConfigurator.
 */
class PrintConfigurator
{
    public function calculate_price_for_dom($data)
    {
        $rpc_data = new PrintConfiguratorData();
        $basics = $rpc_data->getData();

        return 'yeah';
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
