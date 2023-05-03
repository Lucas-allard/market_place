<?php

namespace App\Entity;


class OrderItem extends AbstractEntity {
    /**
     * @var int|null
     */
    private ?int $quantity = 0;
    /**
     * @var float|null
     */
    private ?float $price = 0.0;
    /**
     * @var Product|null
     */
    private ?Product $product = null;

    /**
     * @var Order|null
     */
    private ?Order $order = null;

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
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return OrderItem
     */
    public function setProduct(Product $product): OrderItem
    {
        $this->product = $product;
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


}