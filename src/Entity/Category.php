<?php

namespace App\Entity;

use App\Annotation\SlugProperty;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @SlugProperty(property="name")
 */
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends AbstractEntity
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['product:list'])]
    private string $name = '';

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    private string $description = '';

    /**
     * @var Category|null
     */
    #[ORM\ManyToOne(targetEntity: self::class, cascade: ['persist', 'remove'], inversedBy: 'children')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?self $parent = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection|ArrayCollection $children;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Brand::class, mappedBy: 'categories')]
    private Collection|ArrayCollection $brands;

    /**
     * @var Product
     */
    private Product $bestProduct;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'categories')]
    private Collection|ArrayCollection $products;

    public function __construct()
    {
        parent::__construct();
        $this->children = new ArrayCollection();
        $this->brands = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getParent() ? $this->getParent()->getName() . ' > ' . $this->getName() : $this->getName();
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
     * @return self|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param Category|null $parent
     * @return $this
     */
    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Category $child
     * @return $this
     */
    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    /**
     * @param Category $child
     * @return $this
     */
    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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
     * @return Collection<int, Brand>
     */
    public function getBrands(): Collection
    {
        return $this->brands;
    }

    /**
     * @param Brand $brand
     * @return $this
     */
    public function addBrand(Brand $brand): self
    {
        if (!$this->brands->contains($brand)) {
            $this->brands->add($brand);
            $brand->addCategory($this);
        }

        return $this;
    }

    /**
     * @param Brand $brand
     * @return $this
     */
    public function removeBrand(Brand $brand): self
    {
        if ($this->brands->removeElement($brand)) {
            $brand->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Product
     */
    public function getBestProduct(): Product
    {
        return $this->bestProduct;
    }

    /**
     * @param Product $bestProduct
     * @return Category
     */
    public function setBestProduct(Product $bestProduct): Category
    {
        $this->bestProduct = $bestProduct;

        return $this;
    }


    /**
     * @return int
     */
    public function getTotalProducts(): int
    {
        return $this->products->count();
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
        }

        return $this;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }
}