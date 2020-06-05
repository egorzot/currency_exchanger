<?php

namespace App\Entity;

use App\Repository\BankAmountRepository;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * @ORM\Entity(repositoryClass=BankAmountRepository::class)
 */
class BankAmount
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?Money
    {
        if (!$this->currency) {
            return null;
        }
        if (!$this->amount) {
            return new Money(0, new Currency($this->currency));
        }
        return new Money($this->amount, new Currency($this->currency));
    }

    public function setAmount(Money $money): self
    {
        $this->amount = $money->getAmount();
        $this->currency = $money->getCurrency()->getCode();

        return $this;
    }
}
