<?php

namespace App\Entity\Interface;

use App\Entity\Order;

interface PaymentInterface
{
    /**
     * @return float
     */
    public function getAmount(): float;

    /**
     * @return string
     */
    public function getCurrency(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order;

}