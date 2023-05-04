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
     * @return string
     */
    public function getStatus(): string;

    /**
     * @retutests/Service/Stripe/StripConnexionTest.phprn Order|null
     */
    public function getOrder(): ?Order;

}