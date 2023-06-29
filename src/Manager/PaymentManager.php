<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\Payment;
use App\Factory\PaymentFactory;
use App\Repository\PaymentRepository;
use App\Service\Payment\PaymentProcessorInterface;
use Stripe\Checkout\Session;

class PaymentManager
{
    private PaymentFactory $paymentFactory;

    private PaymentProcessorInterface $paymentProcessor;
    private PaymentRepository $paymentRepository;

    public function __construct(
        PaymentFactory $paymentFactory,
        PaymentProcessorInterface $paymentProcessor,
        PaymentRepository $paymentRepository
    )
    {
        $this->paymentFactory = $paymentFactory;
        $this->paymentProcessor = $paymentProcessor;
        $this->paymentRepository = $paymentRepository;
    }
    public function getPayment(Order $order): Payment
    {
        $payment = $order->getPayment();

        if (!$payment) {
            $payment = $this->paymentFactory->create($order);
        }

        return $payment;
    }

    public function getPaymentSession(Payment $payment): Session
    {
        return $this->paymentProcessor->process($payment);
    }

    public function savePayment(Payment $payment): void
    {
        $this->paymentRepository->save($payment);
    }

}