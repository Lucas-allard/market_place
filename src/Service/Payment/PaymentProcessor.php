<?php

namespace App\Service\Payment;

use App\Entity\Interface\PaymentInterface;
use App\Entity\Payment;
use App\Service\Stripe\StripeConnexion;
use Exception;
use LogicException;
use Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentProcessor implements PaymentProcessorInterface
{

    private UrlGeneratorInterface $urlGenerator;
    private Security $security;
    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        StripeConnexion::init();
    }

    /**
     * @param Payment $payment
     * @return Session
     * @throws ApiErrorException
     */
    public function process(PaymentInterface $payment): Session
    {
        $stripeProducts = [];

        foreach ($payment->getOrder()->getOrderItems() as $orderItem) {
            $stripeProducts[] = [
                'price_data' => [
                    'currency' => $payment->getCurrency(),
                    'unit_amount' => $orderItem->getProduct()->getPriceWithDiscount() * 100,
                    'product_data' => [
                        'name' => $orderItem->getProduct()->getName(),
                    ],
                ],
                'quantity' => $orderItem->getQuantity(),
            ];
        }


        return Session::create([
            'customer_email' => $this->security->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => $stripeProducts,
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('app_payment_success', ['id' => $payment->getOrder()->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('app_payment_failed', ['id' => $payment->getOrder()->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}