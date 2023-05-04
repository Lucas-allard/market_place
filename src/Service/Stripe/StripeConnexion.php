<?php

namespace App\Tests\Service\Stripe;

class StripeConnexion
{
    private string $secretKey = "";
    private string $publicKey = "";

    public function __construct()
    {
        $this->secretKey = $_ENV['STRIPE_SECRET'];
        $this->publicKey = $_ENV['STRIPE_KEY'];
    }

    
}