<?php

namespace App\Entity;

class Product extends AbstractEntity
{
    private string $name = '';
    private string $description = '';
    private float $price = 0.0;
    private int $quantity = 0;
    private ?array $categories = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription(string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Product
     */
    public function setPrice(float $price): Product
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Product
     */
    public function setQuantity(int $quantity): Product
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @param int $quantity
     * @return Product
     */
    public function addQuantity(int $quantity): Product
    {
        $this->quantity += $quantity;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * @param Category[]|null $categories
     * @return Product
     */
    public function setCategories(?array $categories): Product
    {
        foreach ($categories as $category) {
            $this->addCategory($category);
        }
        return $this;
    }

    /**
     * @param Category $category
     * @return Product
     */
    public function addCategory(Category $category): Product
    {
        if (!in_array($category, $this->categories, true)) {
            $this->categories[] = $category;
        }

        return $this;
    }

}