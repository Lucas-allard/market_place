<?php

namespace App\Entity\Interface;

interface OrderItemInterface
{
    public function getQuantity(): ?int;

    public function getPrice(): ?float;

    public function getProduct();

    public function getOrder(): ?OrderInterface;
}