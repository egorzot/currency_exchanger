<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Money\Currency;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=36)
     */
    private $uuid;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isExecuted = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expireAt;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Currency()
     */
    private $fromCurrency;

    /**
     * @ORM\Column(type="integer")
     */
    private $fromAmount;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Currency()
     */
    private $toCurrency;

    /**
     * @ORM\Column(type="integer")
     */
    private $toAmount;

    /**
     * @ORM\Column(type="float")
     */
    private $rate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="conversations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getIsExecuted(): ?bool
    {
        return $this->isExecuted;
    }

    public function setIsExecuted(bool $isExecuted): self
    {
        $this->isExecuted = $isExecuted;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeInterface
    {
        return $this->expireAt;
    }

    public function setExpireAt(\DateTimeInterface $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getFromAmount(): ?Money
    {
        if (!$this->fromCurrency) {
            return null;
        }
        if (!$this->fromAmount) {
            return new Money(0, new Currency($this->fromCurrency));
        }
        return new Money($this->fromAmount, new Currency($this->fromCurrency));
    }

    public function setFromAmount(Money $money): self
    {
        $this->fromAmount = $money->getAmount();
        $this->fromCurrency = $money->getCurrency()->getCode();

        return $this;
    }

    public function getToAmount(): ?Money
    {
        if (!$this->toCurrency) {
            return null;
        }
        if (!$this->toAmount) {
            return new Money(0, new Currency($this->toCurrency));
        }
        return new Money($this->toAmount, new Currency($this->toCurrency));
    }

    public function setToAmount(Money $money): self
    {
        $this->toAmount = $money->getAmount();
        $this->toCurrency = $money->getCurrency()->getCode();

        return $this;
    }

    public function getRate():float
    {
        return $this->rate;
    }

    public function setRate($rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
