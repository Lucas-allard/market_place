<?php

namespace App\Entity;

use App\Annotation\SlugProperty;
use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @SlugProperty(property="name")
 */
#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand extends AbstractEntity
{
    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Picture
     */
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Picture $picture;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'brands')]
    private Collection|ArrayCollection $categories;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Product::class, orphanRemoval: true)]
    private Collection|ArrayCollection $products;


    public function __construct()
    {
        parent::__construct();
        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
     * @return Picture
     */
    public function getPicture(): Picture
    {
        return $this->picture;
    }

    /**
     * @param Picture $picture
     * @return $this
     */
    public function setPicture(Picture $picture): self
    {
        $this->picture = $picture;

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
        }

        return $this;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

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
    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setBrand($this);
        }

        return $this;
    }

    /**
     * @param Collection $products
     * @return $this
     */
    public function setProducts(Collection $products): static
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
    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getBrand() === $this) {
                $product->setBrand(null);
            }
        }

        return $this;
    }

}
