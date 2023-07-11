<?php

namespace App\Entity;


use App\Annotation\SlugProperty;
use App\Repository\OrderRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Utils;

/**
 * @SlugProperty(property="createdAt")
 */
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
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $status = self::STATUS_CART;

    /**
     * @var Customer|null
     */
    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    private ?Customer $customer = null;

    /**
     * @var Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Collection $orderItems;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItemSeller::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $orderItemSellers;

    /**
     * @var Payment|null
     */
    #[ORM\OneToOne(inversedBy: 'order', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    /**
     * @var float|int
     */
    private float $total = 0;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $slug = null;
    const STATUS_CART = 'cart';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ON_DELIVERY = 'on_delivery';

    public function __construct()
    {
        parent::__construct();
        $this->orderItems = new ArrayCollection();
        $this->orderItemSellers = new ArrayCollection();
        $this->status = self::STATUS_CART;
        $this->createdAt = new DateTime();
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
    public function getShippingDate(): ?DateTimeInterface
    {
        return $this->shippingDate;
    }

    /**
     * @param DateTimeInterface|null $shippingDate
     * @return Order
     */
    public function setShippingDate(?DateTimeInterface $shippingDate): Order
    {
        $this->shippingDate = $shippingDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Order
     */
    public function setStatus(?string $status): Order
    {
        $this->status = $status;
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
     * @return Collection<OrderItemSeller>
     */
    public function getOrderItemSellers(): Collection
    {
        return $this->orderItemSellers;
    }

    /**
     * @param int $id
     * @return OrderItemSeller|null
     */
    public function getOrderItemSeller(int $id): ?OrderItemSeller
    {
        foreach ($this->getOrderItemSellers() as $orderItemSeller) {
            if ($orderItemSeller->getSeller()->getId() === $id) {
                return $orderItemSeller;
            }
        }
        return null;

    }
    /**
     * @param Collection $orderItemSellers
     * @return Order
     */
    public function setOrderItemSellers(Collection $orderItemSellers): Order
    {
        foreach ($orderItemSellers as $orderItemSeller) {
            $this->addOrderItemSeller($orderItemSeller);
        }
        return $this;
    }

    /**
     * @param OrderItemSeller $orderItemSeller
     * @return Order
     */
    public function addOrderItemSeller(OrderItemSeller $orderItemSeller): Order
    {
        foreach ($this->getOrderItemSellers() as $existingOrderItemSeller) {
            if ($existingOrderItemSeller->getSeller() === $orderItemSeller->getSeller()) {
                return $this;
            }
        }
        $this->orderItemSellers->add($orderItemSeller);
        $orderItemSeller->setOrder($this);

        return $this;
    }

    /**
     * @param OrderItemSeller $orderItemSeller
     * @return Order
     */
    public function removeOrderItemSeller(OrderItemSeller $orderItemSeller): Order
    {
        $this->orderItemSellers->removeElement($orderItemSeller);
        return $this;
    }

    /**
     * @return $this
     */
    public function removeOrderItemSellers(): Order
    {
        $this->orderItemSellers->clear();
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

    /**
     * @param int $id
     * @return float
     */
    public function getTotalForSeller(int $id): float
    {
        $total = [];

        // parcourir chaque orderItem et vérifier dans chaque produit si le seller du produit exite dans le tableau, si oui faire le total des produits du seller
        foreach ($this->getOrderItems() as $orderItem) {
            $seller = $orderItem->getProduct()->getSeller();
            if (array_key_exists($seller->getId(), $total)) {
                $total[$seller->getId()] += $orderItem->getTotal();
            } else {
                $total[$seller->getId()] = $orderItem->getTotal();
            }
        }

        return $total[$id];
    }


    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}