<?php

namespace App\Tests\Service;

use App\Service\LocalJsonExchangeProvider;
use PHPUnit\Framework\TestCase;
use Money\Exchange\FixedExchange;

class LocalJsonExchangeProviderTest extends TestCase
{

    public function testGetExchange(): void
    {
        $provider = new LocalJsonExchangeProvider(__DIR__ . '/../resources/rates.json');
        $fixedExchanger = new FixedExchange(['USD' => ['RUB' => 75]]);
        $this->assertEquals($provider->getExchange(), $fixedExchanger);
    }
}
