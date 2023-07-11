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
    /**
     * @var PaymentFactory
     */
    private PaymentFactory $paymentFactory;

    /**
     * @var PaymentProcessorInterface
     */
    private PaymentProcessorInterface $paymentProcessor;
    /**
     * @var PaymentRepository
     */
    private PaymentRepository $paymentRepository;

    /**
     * @param PaymentFactory $paymentFactory
     * @param PaymentProcessorInterface $paymentProcessor
     * @param PaymentRepository $paymentRepository
     */
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

    /**
     * @param Order $order
     * @return Payment
     */
    public function getPayment(Order $order): Payment
    {
        $payment = $order->getPayment();

        if (!$payment) {
            $payment = $this->paymentFactory->create($order);
        }

        return $payment;
    }

    /**
     * @param Payment $payment
     * @return Session
     */
    public function getPaymentSession(Payment $payment): Session
    {
        return $this->paymentProcessor->process($payment);
    }

    /**
     * @param Payment $payment
     * @return void
     */
    public function savePayment(Payment $payment): void
    {
        $this->paymentRepository->save($payment);
    }

}