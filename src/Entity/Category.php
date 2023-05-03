<?php

namespace App\Entity;

class Category extends AbstractEntity
{
    /**
     * @var string
     */
    private string $name = '';
    /**
     * @var string
     */
    private string $description = '';
    /**
     * @var Category|null
     */
    private ?Category $parent = null;
    /**
     * @var Category[]
     */
    private array $children = [];
    /**
     * @var array
     */
    private array $products = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Category
     */
    public function setName(string $name): Category
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
     * @return Category
     */
    public function setDescription(string $description): Category
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Category|null
     */
    public function getParent(): ?Category
    {
        return $this->parent;
    }

    /**
     * @param Category $parent
     * @return $this
     */
    public function setParent(Category $parent): Category
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Category[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param Category[] $children
     * @return Category
     */
    public function setChildren(array $children): Category
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
        return $this;
    }

    /**
     * @param Category $child
     * @return Category
     */
    public function addChild(Category $child): Category
    {
        if (!in_array($child, $this->children, true)) {
            $this->children[] = $child;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param array $products
     * @return Category
     */
    public function setProducts(array $products): Category
    {
        foreach ($products as $product) {
            $this->addProduct($product);
        }
        return $this;
    }

    /**
     * @param  $product
     * @return Category
     */
    public function addProduct($product): Category
    {
        if (!in_array($product, $this->products)) {
            $this->products[] = $product;
        }
        return $this;
    }
}