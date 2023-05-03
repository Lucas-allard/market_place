<?php

namespace App\Entity;

use App\Entity\Interface\CustomerInterface;
use App\Entity\Interface\OrderInterface;
use DateTimeInterface;

class Customer extends User implements CustomerInterface
{
    /**
     * @var string|null
     */
    private ?string $shippingAddress = "";
    /**
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $birthDate = null;
    /**
     * @var OrderInterface[]
     */
    private array $orders = [];

    /**
     * @return string|null
     */
    public function getShippingAddress(): ?string
    {
        return $this->shippingAddress;
    }

    /**
     * @param string|null $shippingAddress
     * @return Customer
     */
    public function setShippingAddress(?string $shippingAddress): Customer
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }


    /**
     * @return DateTimeInterface|null
     */
    public function getBirthDate(): ?DateTimeInterface
    {
        return $this->birthDate;
    }

    /**
     * @param DateTimeInterface|null $birthDate
     * @return Customer
     */
    public function setBirthDate(?DateTimeInterface $birthDate): Customer
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * @return OrderInterface[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @param OrderInterface[] $orders
     * @return Customer
     */
    public function setOrders(array $orders): Customer
    {
        foreach ($orders as $order) {
            $this->addOrder($order);
        }
        return $this;
    }

    /**
     * @param OrderInterface $order
     * @return $this
     */
    public function addOrder(OrderInterface $order): Customer
    {
        if (!in_array($order, $this->orders, true)) {
            $this->orders[] = $order;
        }
        return $this;
    }

}