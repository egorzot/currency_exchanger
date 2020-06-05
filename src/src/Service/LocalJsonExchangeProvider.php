<?php

namespace App\Service;


use Money\Exchange\FixedExchange;

class LocalJsonExchangeProvider implements ExchangeProvider
{
    private $fixedExchange;

    /**
     * LocalJsonRateProvider constructor.
     * @param $localJsonFilePath
     */
    public function __construct($localJsonFilePath)
    {
        $content = file_get_contents($localJsonFilePath);
        $rates = json_decode($content, true);

        $list = [];
        foreach ($rates as $currencyPair => $rate) {
            [$cur1, $cur2] = explode('/', $currencyPair);
            if (!isset($list[$cur1])) {
                $list[$cur1] = [$cur2 => $rate];
            } elseif (!isset($list[$cur1][$cur2])) {
                $list[$cur1][$cur2] = $rate;
            }
        }
        $this->fixedExchange = new FixedExchange($list);
    }

    public function getExchange(): FixedExchange
    {
        return $this->fixedExchange;
    }
}
