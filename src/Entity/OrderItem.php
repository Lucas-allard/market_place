<?php

namespace App\Entity;

use App\Annotation\SlugProperty;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @SlugProperty(property="id")
 */
#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem extends AbstractEntity {

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer')]
    private ?int $quantity = 0;

    /**
     * @var Order|null
     */
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    /**
     * @var Product|null
     */
    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int|null $quantity
     * @return OrderItem
     */
    public function setQuantity(?int $quantity): OrderItem
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param int $quantity
     * @return $this
     */
    public function addQuantity(int $quantity = 1): OrderItem
    {
        $this->quantity += $quantity;
        return $this;
    }


    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     * @return OrderItem
     */
    public function setOrder(?Order $order): OrderItem
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return $this
     */
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @param OrderItem $item
     * @return bool
     */
    public function equals(OrderItem $item): bool
    {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        if ($this->getProduct()->getPriceWithDiscount() !== null) {
            return $this->getProduct()->getPriceWithDiscount() * $this->getQuantity();
        }

        return $this->getProduct()->getPrice() * $this->getQuantity();
    }
}