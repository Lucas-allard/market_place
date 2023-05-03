<?php

namespace App\Entity;

interface CustomerInterface
{
    public function getShippingAddress(): ?string;
    public function getDateOfBirth(): ?\DateTimeInterface;

    /**
     * @todo Ajout de la méthode getOrders()
     */
}