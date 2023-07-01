<?php

namespace App\Service\Payment;

use App\Entity\Interface\PaymentInterface;
use App\Entity\Payment;
use App\Factory\StripeCheckoutSessionFactory;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripePaymentProcessor implements PaymentProcessorInterface
{
    private StripeCheckoutSessionFactory $stripeCheckoutSessionFactory;

    public function __construct(StripeCheckoutSessionFactory $stripeCheckoutSessionFactory)
    {
        $this->stripeCheckoutSessionFactory = $stripeCheckoutSessionFactory;
    }

    /**
     * @param Payment $payment
     * @return Session
     * @throws ApiErrorException
     */
    public function process(PaymentInterface $payment): Session
    {
        return $this->stripeCheckoutSessionFactory->create($payment);
    }
}