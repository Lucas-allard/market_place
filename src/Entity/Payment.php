<?php

namespace App\Entity;

use App\Annotation\SlugProperty;
use App\Entity\Interface\PaymentInterface;
use App\Repository\PaymentRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @SlugProperty(property="id")
 */
#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[UniqueEntity('paymentToken', message: 'Le token de paiement doit être unique')]
class Payment extends AbstractEntity implements PaymentInterface
{
    /**
     * @var float
     */
    #[ORM\Column(type: 'float')]
    private float $amount = 0.0;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = self::CURRENCY_EUR;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    private string $description = '';

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $status = '';

    /**
     * @var Order|null
     */
    #[ORM\OneToOne(mappedBy: 'payment', cascade: ['persist', 'remove'])]
    private ?Order $order = null;

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const CURRENCY_EUR = 'EUR';

    const CREDIT_CARD = 'credit_card';
    const PAYPAL = 'paypal';

    public function __construct()
    {
        parent::__construct();
        $this->createdAt = new DateTime();
    }
    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return Payment
     */
    public function setAmount(float $amount): Payment
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Payment
     */
    public function setCurrency(string $currency): Payment
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Payment
     */
    public function setDescription(string $description): Payment
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Payment
     */
    public function setStatus(?string $status): Payment
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     * @return $this
     */
    public function setOrder(?Order $order): self
    {
        // unset the owning side of the relation if necessary
        if ($order === null && $this->order !== null) {
            $this->order->setPayment(null);
        }

        // set the owning side of the relation if necessary
        if ($order !== null && $order->getPayment() !== $this) {
            $order->setPayment($this);
        }

        $this->order = $order;

        return $this;
    }
}