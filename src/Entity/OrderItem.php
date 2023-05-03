<?php

namespace App\Entity;

use App\Entity\Interface\OrderInterface;

class OrderItem extends AbstractEntity implements Interface\OrderItemInterface
{
    private ?int $quantity = 0;
    private ?float $price = 0.0;
    private $product = null;
    private ?OrderInterface $order = null;

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

    public function addQuantity(int $quantity = 1): OrderItem
    {
        $this->quantity += $quantity;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     * @return OrderItem
     */
    public function setPrice(?float $price): OrderItem
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return null
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param  $product
     * @return OrderItem
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return OrderInterface|null
     */
    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    /**
     * @param OrderInterface|null $order
     * @return OrderItem
     */
    public function setOrder(?OrderInterface $order): OrderItem
    {
        $this->order = $order;
        return $this;
    }


}