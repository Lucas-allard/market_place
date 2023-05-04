<?php

namespace App\Service\Stripe;

use Stripe\Stripe;

class StripeConnexion
{
    private static string $apiKey;

    public static function init(): void
    {
        self::$apiKey = $_ENV['STRIPE_SECRET_KEY'];
        Stripe::setApiKey(self::$apiKey);
    }
}