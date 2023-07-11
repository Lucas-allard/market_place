<?php

namespace App\Factory;

use App\Entity\Interface\PaymentInterface;
use App\Entity\Order;
use App\Entity\Payment;

class PaymentFactory
{
    /**
     * @param Order $order
     * @return PaymentInterface
     */
    public function create(Order $order): PaymentInterface
    {
        $payment = new Payment();
        $payment
            ->setStatus(Payment::STATUS_PENDING)
            ->setAmount($order->getTotal())
            ->setCurrency(Payment::CURRENCY_EUR)
            ->setDescription('Order #' . $order->getId())
            ->setOrder($order)
        ;

        return $payment;
    }


}