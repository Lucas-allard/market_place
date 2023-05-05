<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product extends AbstractEntity
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    private string $description = '';

    /**
     * @var float
     */
    #[ORM\Column(type: 'float')]
    private float $price = 0.0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $quantity = 0;

    /**
     * @var Category[]|null
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinTable(name: 'product_category')]
    private ?array $categories = [];

    /**
     * @var Seller|null
     */
    #[ORM\ManyToOne(targetEntity: Seller::class, inversedBy: 'products')]
    private ?Seller $seller = null;


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
     * @return Category[]|null
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

    /**
     * @return Seller|null
     */
    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    /**
     * @param Seller|null $seller
     * @return Product
     */
    public function setSeller(?Seller $seller): Product
    {
        $this->seller = $seller;
        return $this;
    }

}