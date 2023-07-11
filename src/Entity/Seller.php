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
    private float $rating = 0.0;

    /**
     * @var Collection|ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Product::class, orphanRemoval: true)]
    private Collection $products;

    /**
     * @var Collection|ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: OrderItemSeller::class, orphanRemoval: true)]
    private Collection $orderItemSellers;


    public function __construct()
    {
        parent::__construct();
        $this->products = new ArrayCollection();
        $this->setRoles(['ROLE_SELLER']);
        $this->orderItemSellers = new ArrayCollection();
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
    public function getRating(): ?float
    {
        return $this->rating;
    }

    /**
     * @param float $sellerRating
     * @return Seller
     */
    public function setRating(float $rating): Seller
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setSeller($this);
        }

        return $this;
    }

    /**
     * @param Product $product
     * @return $this
     */
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

    /**
     * @return Collection<int, OrderItemSeller>
     */
    public function getOrderItemSellers(): Collection
    {
        return $this->orderItemSellers;
    }

    /**
     * @param OrderItemSeller $orderItemSeller
     * @return $this
     */
    public function addOrderItemSeller(OrderItemSeller $orderItemSeller): static
    {
        if (!$this->orderItemSellers->contains($orderItemSeller)) {
            $this->orderItemSellers->add($orderItemSeller);
            $orderItemSeller->setSeller($this);
        }

        return $this;
    }

    /**
     * @param OrderItemSeller $orderItemSeller
     * @return $this
     */
    public function removeOrderItemSeller(OrderItemSeller $orderItemSeller): static
    {
        if ($this->orderItemSellers->removeElement($orderItemSeller)) {
            // set the owning side to null (unless already changed)
            if ($orderItemSeller->getSeller() === $this) {
                $orderItemSeller->setSeller(null);
            }
        }

        return $this;
    }


}