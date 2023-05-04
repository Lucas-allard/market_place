<?php

namespace App\Service\Payment;

use App\Entity\Interface\PaymentInterface;
use App\Entity\Payment;
use App\Service\Stripe\StripeConnexion;
use Exception;
use LogicException;
use Stripe;
use Stripe\Exception\ApiErrorException;

class PaymentProcessor implements PaymentProcessorInterface
{

    public function __construct()
    {
        StripeConnexion::init();
    }

    /**
     * @param PaymentInterface $payment
     * @return bool
     */
    public function process(PaymentInterface $payment): bool
    {
        $isSuccess = false;
        $this->checkPaymentStatus($payment->getStatus());

        try {
            $this->chargePayment($payment);
            $payment->setStatus(Payment::STATUS_PAID);
            $isSuccess = true;
        } catch (Exception $e) {
            $payment->setStatus(Payment::STATUS_FAILED);
            print_r($e->getMessage());
        }

        return $isSuccess;
    }

    public function checkPaymentStatus(string $status): void
    {
        if ($status !== Payment::STATUS_PENDING) {
            throw new LogicException('Payment is not pending');
        }
    }

    /**
     * @param PaymentInterface $payment
     * @return void
     * @throws Exception
     */
    private function chargePayment(PaymentInterface $payment): void
    {
        try {
            $charge = Stripe\Charge::create([
                'amount' => $payment->getAmount(),
                'currency' => $payment->getCurrency(),
                'description' => $payment->getDescription(),
                'source' => $payment->getPaymentToken(),
            ]);
            if ($charge->status !== 'succeeded') {
                throw new Exception('Payment failed');
            }
        } catch (ApiErrorException $e) {
            throw new Exception($e->getMessage());
        }
    }
}