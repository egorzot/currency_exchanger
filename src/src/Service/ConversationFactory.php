<?php


namespace App\Service;


use App\Entity\Conversation;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ConversationFactory
{
    private $converter;
    private $exchangeProvider;
    private $tokenStorage;

    /**
     * ConversationFactory constructor.
     * @param ExchangeProvider $exchangeProvider
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(ExchangeProvider $exchangeProvider, TokenStorageInterface $tokenStorage)
    {
        $this->exchangeProvider = $exchangeProvider;
        $this->tokenStorage = $tokenStorage;
        $this->converter = new Converter(new ISOCurrencies(), $exchangeProvider->getExchange());
    }

    public function createFromJson($json): Conversation
    {
        $data = json_decode($json, true);
        $fromAmount = (int)($data['fromAmount']['amount'] * 100);
        $fromCurrency = new Currency($data['fromAmount']['currency']);
        $toCurrency = new Currency($data['toCurrency']);
        $rate = $this->exchangeProvider->getExchange()->quote($fromCurrency, $toCurrency)->getConversionRatio();

        $conversation = new Conversation();
        $fromMoney = new Money($fromAmount, $fromCurrency);
        $toMoney = $this->converter->convert($fromMoney, $toCurrency);
        $conversation->setFromAmount($fromMoney);
        $conversation->setRate($rate);
        $conversation->setToAmount($toMoney);
        $conversation->setUser($this->tokenStorage->getToken()->getUser());
        $conversation->setExpireAt((new \DateTime('+ 1 minute')));

        return $conversation;
    }


}
