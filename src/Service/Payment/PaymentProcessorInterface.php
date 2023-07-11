<?php

namespace App\Service\Payment;

use App\Entity\Interface\PaymentInterface;

interface PaymentProcessorInterface
{
    /**
     * @param PaymentInterface $payment
     * @return mixed
     */
    public function process(PaymentInterface $payment): mixed;
}