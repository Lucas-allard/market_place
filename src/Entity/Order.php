<?php

namespace App\Entity;

use DateTimeInterface;

class Order extends AbstractEntity implements OrderInterface
{
    /**
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $orderDate = null;
    /**
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $deliveryDate = null;
    /**
     * @var string
     */
    private string $orderStatus = '';
    /**
     * @var float
     */
    private float $totalAmount = 0.0;
    /**
     * @var CustomerInterface|null
     */
    private ?CustomerInterface $customer = null;
    /**
     * @var array
     */
    private array $orderItems = [];

    /**
     *
     */
    const STATUS_PENDING = 'pending';
    /**
     *
     */
    const STATUS_PAID = 'paid';


    /**
     * @return DateTimeInterface|null
     */
    public function getOrderDate(): ?DateTimeInterface
    {
        return $this->orderDate;
    }

    /**
     * @param DateTimeInterface|null $orderDate
     * @return Order
     */
    public function setOrderDate(?DateTimeInterface $orderDate): Order
    {
        $this->orderDate = $orderDate;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDeliveryDate(): ?DateTimeInterface
    {
        return $this->deliveryDate;
    }

    /**
     * @param DateTimeInterface|null $deliveryDate
     * @return Order
     */
    public function setDeliveryDate(?DateTimeInterface $deliveryDate): Order
    {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderStatus(): string
    {
        return $this->orderStatus;
    }

    /**
     * @param string $orderStatus
     * @return Order
     */
    public function setOrderStatus(string $orderStatus): Order
    {
        $this->orderStatus = $orderStatus;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     * @return Order
     */
    public function setTotalAmount(float $totalAmount): Order
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    /**
     * @return CustomerInterface|null
     */
    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    /**
     * @param CustomerInterface|null $customer
     * @return Order
     */
    public function setCustomer(?CustomerInterface $customer): Order
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    /**
     * @param array $orderItems
     * @return Order
     */
    public function setOrderItems(array $orderItems): Order
    {
        $this->orderItems = $orderItems;
        return $this;
    }

}