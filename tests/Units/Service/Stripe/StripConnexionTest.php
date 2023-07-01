<?php

namespace App\Tests\Units\Service\Stripe;

use PHPUnit\Framework\TestCase;
use Stripe\Stripe;

class StripConnexionTest extends TestCase
{

    /**
     * @group service
     * @group stripe
     * @group stripe-connexion
     * @group stripe-connexion-init
     */
    public function testStripeConnexionGetStripe(): void
    {
        $apiKey = $_ENV['STRIPE_SECRET_KEY'];

        \App\Bundle\Stripe\StripeConnexion::init();

        $this->assertSame($apiKey, Stripe::$apiKey);

    }
}