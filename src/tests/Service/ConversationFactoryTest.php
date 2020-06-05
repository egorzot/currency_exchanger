<?php

namespace App\Tests\Service;

use App\Entity\Conversation;
use App\Entity\User;
use App\Service\ConversationFactory;
use App\Service\LocalJsonExchangeProvider;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ConversationFactoryTest extends TestCase
{

    public function testCreateFromJsonRequest(): void
    {
        $factory = $this->getConversationFactory();

        $json = [
            'fromAmount' => [
                'currency' => 'USD',
                'amount' => '100.12'
            ],
            'toCurrency' => 'RUB'
        ];

        $conversationFromFactory = $factory->createFromJson(json_encode($json));

        $handyConversation = (new Conversation())
            ->setRate(75)
            ->setUser(new User())
            ->setFromAmount(Money::USD(10012))
            ->setToAmount(Money::RUB(750900))
            ->setExpireAt($conversationFromFactory->getExpireAt());

        $this->assertEquals($conversationFromFactory, $handyConversation);
    }

    private function getConversationFactory(): ConversationFactory
    {
        $exchangeProvider = new LocalJsonExchangeProvider(__DIR__ . '/../resources/rates.json');

        $tokenInterface = $this->createMock(TokenInterface::class);
        $tokenInterface->method('getUser')
            ->willReturn((new User()));

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')
            ->willReturn($tokenInterface);

        return new ConversationFactory($exchangeProvider, $tokenStorage);
    }
}
