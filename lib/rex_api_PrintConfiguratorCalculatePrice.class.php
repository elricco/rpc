<?php

/**
 * Class PrintConfiguratorGetData.
 */
class rex_api_PrintConfiguratorCalculatePrice extends rex_api_function
{
    protected $published = true;

    /**
     * @return rex_api_result|void
     */
    public function execute()
    {
        $price_data = $_GET['data'];

        $rpc = new PrintConfigurator();
        $data = $rpc->calculate_price_for_dom($price_data);
        if (!is_array($data)) {
            $result = ['errorcode' => 1, rex_i18n::msg('rpc_no_price_calculated')];
            self::httpError($result);
        }
        $result = $data;

        header('Content-Type: application/json; charset=UTF-8');
        exit(json_encode($result));
    }

    /**
     * @param $result
     */
    public static function httpError($result)
    {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        exit(json_encode($result));
    }
}
