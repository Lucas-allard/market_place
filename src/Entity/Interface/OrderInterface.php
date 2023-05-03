<?php

namespace App\Entity\Interface;

use DateTimeInterface;

interface OrderInterface
{
    public function getOrderDate(): ?DateTimeInterface;
    public function getDeliveryDate(): ?DateTimeInterface;
    public function getOrderStatus(): string;
    public function getTotalAmount(): float;
    public function getCustomer(): ?CustomerInterface;
    public function getOrderItems(): array;
    /**
     * @todo Implémenter une méthode getPayment() qui retourne un objet de type PaymentInterface
     */
//    public function getPayment();
}