<?php

namespace App\Entity;


class Seller extends User
{
    /**
     * @var string
     */
    private string $company = '';
    /**
     * @var string
     */
    private string $siret = '';
    /**
     * @var string
     */
    private string $vat = '';
    /**
     * @var float
     */
    private float $sellerRating = 0.0;
    /**
     * @var Product[]
     */
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