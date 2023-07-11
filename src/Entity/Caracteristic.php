<?php

namespace App\Entity;

use App\Repository\CaracteristicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CaracteristicRepository::class)]
class Caracteristic extends AbstractEntity
{

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $value = null;

    /**
     * @var ArrayCollection|Collection
     */
    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'caracteristics')]
    private Collection|ArrayCollection $products;

    public function __construct()
    {
        parent::__construct();
        $this->products = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

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
            $product->addCaracteristic($this);
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
            $product->removeCaracteristic($this);
        }

        return $this;
    }
}
