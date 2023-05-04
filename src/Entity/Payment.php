<?php

namespace App\Entity;

use App\Entity\Interface\PaymentInterface;

class Payment extends AbstractEntity implements PaymentInterface
{
    /**
     * @var float
     */
    private float $amount = 0.0;
    /**
     * @var string
     */
    private string $currency = self::CURRENCY_EUR;

    /**
     * @var string
     */
    private string $paymentToken = '';

    /**
     * @var string
     */
    private string $description = '';
    /**
     * @var string
     */
    private string $status = '';

    /**
     * @var Order|null
     */
    private ?Order $order = null;

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const CURRENCY_EUR = 'EUR';

    const CREDIT_CARD = 'credit_card';
    const PAYPAL = 'paypal';

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
    public function getPaymentToken(): string
    {
        return $this->paymentToken;
    }

    /**
     * @param string $paymentToken
     * @return Payment
     */
    public function setPaymentToken(string $paymentToken): Payment
    {
        $this->paymentToken = $paymentToken;
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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Payment
     */
    public function setStatus(string $status): Payment
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
     * @param Order $order
     * @return Payment
     */
    public function setOrder(Order $order): Payment
    {
        $this->order = $order;
        return $this;
    }
}