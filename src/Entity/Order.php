<?php

namespace App\Entity;


use App\Entity\Interface\PaymentInterface;
use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Order extends AbstractEntity
{
    /**
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $orderDate = null;

    /**
     * @var DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $deliveryDate = null;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $orderStatus = '';

    /**
     * @var float
     */
    #[ORM\Column(type: 'float')]
    private float $totalAmount = 0.0;

    /**
     * @var Customer|null
     */
    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    private ?Customer $customer = null;

    /**
     * @var OrderItem[]
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class)]
    private array $orderItems = [];


    /**
     * @var Payment|null
     */
    #[ORM\OneToOne(inversedBy: 'order', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';

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
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer|null $customer
     * @return Order
     */
    public function setCustomer(?Customer $customer): Order
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return OrderItem[]
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    /**
     * @param OrderItem[] $orderItems
     * @return Order
     */
    public function setOrderItems(array $orderItems): Order
    {
        foreach ($orderItems as $orderItem) {
            $this->addOrderItem($orderItem);
        }
        return $this;
    }

    /**
     * @param OrderItem $orderItem
     * @return Order
     */
    public function addOrderItem(OrderItem $orderItem): Order
    {
        if (!in_array($orderItem, $this->orderItems, true)) {
            $this->orderItems[] = $orderItem;
        }
        return $this;
    }

    /**
     * @return PaymentInterface|null
     */
    public function getPayment(): ?PaymentInterface
    {
        return $this->payment;
    }

    /**
     * @param Payment|null $payment
     * @return Order
     */
    public function setPayment(?Payment $payment): Order
    {
        $this->payment = $payment;
        return $this;
    }
}