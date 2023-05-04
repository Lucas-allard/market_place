<?php

namespace App\Service\Payment;

use App\Entity\Interface\PaymentInterface;

interface PaymentProcessorInterface
{
    public function process(PaymentInterface $payment): bool;
}