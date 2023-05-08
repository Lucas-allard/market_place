<?php

namespace App\Tests\Units\Entity;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    private Category $category;

    protected function setUp(): void
    {
        $this->category = new Category();
    }

    /**
     * @group entity
     * @group category
     * @group category-default
     */
    public function testCategoryDefault(): void
    {
        $this->assertSame('', $this->category->getName());
        $this->assertIsString($this->category->getName());
        $this->assertSame('', $this->category->getDescription());
        $this->assertIsString($this->category->getDescription());
        $this->assertNull($this->category->getParent());
        $this->assertEmpty($this->category->getChildren());
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getChildren());
        $this->assertEmpty($this->category->getProducts());
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getProducts());
    }

    /**
     * @group entity
     * @group category
     * @group category-set-name
     */
    public function testCategorySetName(): void
    {
        $this->category->setName('Category Name');
        $this->assertSame('Category Name', $this->category->getName());
        $this->assertIsString($this->category->getName());
    }

    /**
     * @group entity
     * @group category
     * @group category-set-description
     */
    public function testCategorySetDescription(): void
    {
        $this->category->setDescription('Category Description');
        $this->assertSame('Category Description', $this->category->getDescription());
        $this->assertIsString($this->category->getDescription());
    }

    /**
     * @group entity
     * @group category
     * @group category-set-parent
     */
    public function testCategorySetParent(): void
    {
        $this->category->setParent(new Category());
        $this->assertInstanceOf(Category::class, $this->category->getParent());
    }

    /**
     * @group entity
     * @group category
     * @group category-add-child
     */
    public function testCategoryAddChild(): void
    {
        $this->category->addChild(new Category());
        $this->assertSame(1, count($this->category->getChildren()));
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getChildren());
    }


    /**
     * @group entity
     * @group category
     * @group category-add-child-twice
     */
    public function testCannotAddChildTwice(): void
    {
        $child = new Category();
        $this->category->addChild($child);
        $this->category->addChild($child);
        $this->assertSame(1, count($this->category->getChildren()));
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getChildren());
    }

    /**
     * @group entity
     * @group category
     * @group category-remove-child
     */
    public function testCategoryRemoveChild(): void
    {
        $child = new Category();
        $this->category->addChild($child);
        $this->category->removeChild($child);
        $this->assertSame(0, count($this->category->getChildren()));
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getChildren());
    }


    /**
     * @group entity
     * @group category
     * @group category-add-product
     */
    public function testCategoryAddProduct(): void
    {
        $this->category->addProduct(new Product());
        $this->assertSame(1, count($this->category->getProducts()));
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getProducts());
    }

    /**
     * @group entity
     * @group category
     * @group category-add-product-twice
     */
    public function testCannotAddProductTwice(): void
    {
        $product = new Product();
        $this->category->addProduct($product);
        $this->category->addProduct($product);
        $this->assertSame(1, count($this->category->getProducts()));
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getProducts());
    }

    /**
     * @group entity
     * @group category
     * @group category-remove-product
     */
    public function testCategoryRemoveProduct(): void
    {
        $product = new Product();
        $this->category->addProduct($product);
        $this->category->removeProduct($product);
        $this->assertSame(0, count($this->category->getProducts()));
        $this->assertInstanceOf(ArrayCollection::class, $this->category->getProducts());
    }
}