<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SellerRepository::class)]
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

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $products;



    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

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
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setSeller($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSeller() === $this) {
                $product->setSeller(null);
            }
        }

        return $this;
    }


}