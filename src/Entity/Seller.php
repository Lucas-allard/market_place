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
     * @var array
     */
    private array $products = [];
    /**
     * @var array
     */
    private array $sales = [];

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
     * @return array|null
     */
    public function getProducts(): ?array
    {
        return $this->products;
    }

    /**
     * @param array $products
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
     * @param $product
     * @return $this
     */
    public function addProduct($product): Seller
    {
        if (!in_array($product, $this->products)) {
            $this->products[] = $product;
        }
        return $this;
    }

    /**
     * @return array|null
     */
    public function getSales(): ?array
    {
        return $this->sales;
    }

    /**
     * @param array $sales
     * @return Seller
     */
    public function setSales(array $sales): Seller
    {
        foreach ($sales as $sale) {
            $this->addSale($sale);
        }

        return $this;
    }

    /**
     * @param $sale
     * @return $this
     */
    public function addSale($sale): Seller
    {
        if (!in_array($sale, $this->sales)) {
            $this->sales[] = $sale;
        }
        return $this;
    }

}