<?php

namespace App\Config\Stripe;

use Stripe\Stripe;

class StripeConnexion
{
    /**
     * @var string
     */
    private static string $apiKey;

    /**
     * @return void
     */
    public static function init(): void
    {
        self::$apiKey = $_ENV['STRIPE_SECRET_KEY'];
        Stripe::setApiKey(self::$apiKey);
    }
}