<?php

namespace App\Entity;


use App\Entity\Interface\PaymentInterface;
use App\Repository\OrderRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
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
    private string $orderStatus = self::STATUS_CART;

    /**
     * @var Customer|null
     */
    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    private ?Customer $customer = null;

    /**
     * @var Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ["persist"], orphanRemoval: true)]
    private ?Collection $orderItems;

    #[ORM\OneToOne(inversedBy: 'order', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    private float $total = 0;
    const STATUS_CART = 'cart';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_DELIVERED = 'delivered';

    public function __construct()
    {
        parent::__construct();
        $this->orderItems = new ArrayCollection();
    }

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
     * @return Collection<OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    /**
     * @param Collection $orderItems
     * @return Order
     */
    public function setOrderItems(Collection $orderItems): Order
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
        foreach ($this->getOrderItems() as $orderItems) {
            // The item already exists, update the quantity
            if ($orderItems->equals($orderItem)) {
                $orderItems->setQuantity(
                    $orderItems->getQuantity() + $orderItem->getQuantity()
                );
                return $this;
            }
        }
        $this->orderItems->add($orderItem);
        $orderItem->setOrder($this);

        return $this;
    }

    /**
     * @param OrderItem $orderItem
     * @return Order
     */
    public function removeOrderItem(OrderItem $orderItem): Order
    {
        $this->orderItems->removeElement($orderItem);
        return $this;
    }

    /**
     * @return $this
     */
    public function removeOrderItems(): Order
    {
        $this->orderItems->clear();
        return $this;
    }

    /**
     * @return Payment|null
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @param Payment|null $payment
     * @return $this
     */
    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        $this->total = 0;

        foreach ($this->getOrderItems() as $orderItem) {
            $this->total += $orderItem->getTotal();
        }

        return $this->total;
    }
}