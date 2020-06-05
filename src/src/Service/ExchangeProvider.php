<?php


namespace App\Service;


use Money\Exchange\FixedExchange;

interface ExchangeProvider
{
    public function getExchange(): FixedExchange;
}
