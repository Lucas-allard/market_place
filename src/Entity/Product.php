<?php

namespace App\Entity;

use App\Annotation\SlugProperty;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @SlugProperty(property="name")
 */
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
    #[Groups(['product:list'])]
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
    #[Groups(['product:list'])]
    private float $price = 0.0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Veuillez saisir la quantité du produit')]
    #[Assert\Positive(message: 'La quantité du produit doit être positive')]
    #[Groups(['product:list'])]
    private int $quantity = 0;


    /**
     * @var Seller|null
     */
    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Seller $seller = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection $orderItems;
    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Picture::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection $pictures;

    /**
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: 'Le pourcentage de réduction doit être positif')]
    #[Groups(['product:list'])]
    private ?int $discount = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Caracteristic::class, inversedBy: 'products')]
    private Collection|ArrayCollection $caracteristics;

    /**
     * @var float|null
     */
    #[ORM\Column(nullable: true)]
    private ?float $shippingFee = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'products',cascade: ['persist'])]
    private Collection|ArrayCollection $categories;

    /**
     * @var Brand|null
     */
    #[ORM\ManyToOne(targetEntity: Brand::class, cascade: ['persist'], inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Brand $brand = null;


    public function __construct()
    {
        parent::__construct();
        $this->updatedAt = new DateTime();
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
     * @return Seller|null
     */
    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    /**
     * @param Seller|null $seller
     * @return $this
     */
    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
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

    /**
     * @param OrderItem $orderItem
     * @return $this
     */
    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setProduct($this);
        }

        return $this;
    }

    /**
     * @param OrderItem $orderItem
     * @return $this
     */
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


    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    /**
     * @param Picture $picture
     * @return $this
     */
    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setProduct($this);
        }

        return $this;
    }

    /**
     * @param Collection $pictures
     * @return $this
     */
    public function setPictures(Collection $pictures): self
    {
        foreach ($pictures as $picture) {
            $this->addPicture($picture);
        }
        return $this;
    }

    /**
     * @param Picture $picture
     * @return $this
     */
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

    /**
     * @return int|null
     */
    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    /**
     * @param int|null $discount
     * @return $this
     */
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

    /**
     * @param Caracteristic $caracteristic
     * @return $this
     */
    public function addCaracteristic(Caracteristic $caracteristic): self
    {
        if (!$this->caracteristics->contains($caracteristic)) {
            $this->caracteristics->add($caracteristic);
        }

        return $this;
    }

    /**
     * @param Caracteristic $caracteristic
     * @return $this
     */
    public function removeCaracteristic(Caracteristic $caracteristic): self
    {
        $this->caracteristics->removeElement($caracteristic);

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceWithDiscount(): float
    {
        if ($this->discount === null) {
            return $this->price;
        }
        $newPrice = $this->price - ($this->price * $this->discount / 100);
        return round($newPrice, 2);
    }

    /**
     * @return float|null
     */
    public function getShippingFee(): ?float
    {
        return $this->shippingFee;
    }

    /**
     * @param float $shippingFee
     * @return $this
     */
    public function setShippingFee(float $shippingFee): self
    {
        $this->shippingFee = $shippingFee;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addProduct($this);
        }

        return $this;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Brand|null
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand|null $brand
     * @return $this
     */
    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @param ArrayCollection|Collection $categories
     * @return $this
     */
    public function setCategories(ArrayCollection|Collection $categories): static
    {
        foreach ($categories as $category) {
            $this->addCategory($category);
        }
        return $this;
    }

    /**
     * @param ArrayCollection|Collection $caracteristics
     * @return $this
     */
    public function setCaracteristics(ArrayCollection|Collection $caracteristics): static
    {
        foreach ($caracteristics as $caracteristic) {
            $this->addCaracteristic($caracteristic);
        }
        return $this;
    }
}