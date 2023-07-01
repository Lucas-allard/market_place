<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product extends AbstractEntity
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'Veuillez saisir le nom du produit')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom du produit doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom du produit doit contenir au maximum {{ limit }} caractères',
    )]
    private string $name = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Veuillez saisir la description du produit')]
    private string $description = '';

    /**
     * @var float
     */
    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: 'Veuillez saisir le prix du produit')]
    #[Assert\Positive(message: 'Le prix du produit doit être positif')]
    private float $price = 0.0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Veuillez saisir la quantité du produit')]
    #[Assert\Positive(message: 'La quantité du produit doit être positive')]
    private int $quantity = 0;


    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'products')]
    private Collection $categories;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Seller $seller = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $orderItems;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Brand $brand = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Picture::class, orphanRemoval: true)]
    private Collection $pictures;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: 'Le pourcentage de réduction doit être positif')]
    private ?int $discount = null;

    #[ORM\ManyToMany(targetEntity: Caracteristic::class, inversedBy: 'products')]
    private Collection $caracteristics;

    #[ORM\Column(nullable: true)]
    private ?float $shippingFee = null;


    public function __construct()
    {
        parent::__construct();
        $this->categories = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->caracteristics = new ArrayCollection();
    }

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
        $this->price = round($price, 2);
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
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addProduct($this);
        }

        return $this;
    }

    public function setCategories(Collection $categories): self
    {
        foreach ($categories as $category) {
            $this->addCategory($category);
        }
        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProduct($this);
        }

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProduct($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setProduct($this);
        }

        return $this;
    }

    public function setPictures(Collection $pictures): self
    {
        foreach ($pictures as $picture) {
            $this->addPicture($picture);
        }
        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getProduct() === $this) {
                $picture->setProduct(null);
            }
        }

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return Collection<int, Caracteristic>
     */
    public function getCaracteristics(): Collection
    {
        return $this->caracteristics;
    }

    public function addCaracteristic(Caracteristic $caracteristic): self
    {
        if (!$this->caracteristics->contains($caracteristic)) {
            $this->caracteristics->add($caracteristic);
        }

        return $this;
    }

    public function removeCaracteristic(Caracteristic $caracteristic): self
    {
        $this->caracteristics->removeElement($caracteristic);

        return $this;
    }

    public function getPriceWithDiscount(): float
    {
        if ($this->discount === null) {
            return $this->price;
        }
        $newPrice = $this->price - ($this->price * $this->discount / 100);
        return round($newPrice, 2);
    }

    public function getShippingFee(): ?float
    {
        return $this->shippingFee;
    }

    public function setShippingFee(float $shippingFee): self
    {
        $this->shippingFee = $shippingFee;

        return $this;
    }
}