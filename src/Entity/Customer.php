<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Customer extends User
{
    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $shippingAddress = "";

    /**
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $birthDate = null;

    /**
     * @var Order[]
     */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Order::class)]
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
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @param Order[] $orders
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
     * @param Order $order
     * @return $this
     */
    public function addOrder(Order $order): Customer
    {
        if (!in_array($order, $this->orders, true)) {
            $this->orders[] = $order;
        }

        return $this;
    }

}