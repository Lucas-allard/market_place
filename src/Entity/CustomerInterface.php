<?php

namespace App\Entity;

interface CustomerInterface
{
    public function getShippingAddress(): ?string;
    public function getBirthDate(): ?\DateTimeInterface;

    public function getOrders(): array;
}