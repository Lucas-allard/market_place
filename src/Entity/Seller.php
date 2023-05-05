<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity('siret', message: "Un vendeur ayant ce numéro de SIRET existe déjà !")]
class Seller extends User
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $company = '';
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $siret = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $vat = '';

    /**
     * @var float
     */
    #[ORM\Column(type: 'float')]
    private float $sellerRating = 0.0;
    /**
     * @var Product[]
     */
    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Product::class)]
    private array $products = [];

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string $company
     * @return Seller
     */
    public function setCompany(string $company): Seller
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiret(): ?string
    {
        return $this->siret;
    }

    /**
     * @param string $siret
     * @return Seller
     */
    public function setSiret(string $siret): Seller
    {
        $this->siret = $siret;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVat(): ?string
    {
        return $this->vat;
    }

    /**
     * @param string $vat
     * @return Seller
     */
    public function setVat(string $vat): Seller
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getSellerRating(): ?float
    {
        return $this->sellerRating;
    }

    /**
     * @param float $sellerRating
     * @return Seller
     */
    public function setSellerRating(float $sellerRating): Seller
    {
        $this->sellerRating = $sellerRating;
        return $this;
    }

    /**
     * @return Product[]|null
     */
    public function getProducts(): ?array
    {
        return $this->products;
    }

    /**
     * @param Product[] $products
     * @return Seller
     */
    public function setProducts(array $products): Seller
    {
        foreach ($products as $product) {
            $this->addProduct($product);
        }

        return $this;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function addProduct(Product $product): Seller
    {
        if (!in_array($product, $this->products, true)) {
            $this->products[] = $product;
        }
        return $this;
    }
}