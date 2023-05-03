<?php

namespace App\Entity;

use DateTimeInterface;

interface CustomerInterface
{
    /**
     * @return string|null
     */
    public function getShippingAddress(): ?string;

    /**
     * @return DateTimeInterface|null
     */
    public function getBirthDate(): ?DateTimeInterface;

    /**
     * @return OrderInterface[]
     */
    public function getOrders(): array;
}