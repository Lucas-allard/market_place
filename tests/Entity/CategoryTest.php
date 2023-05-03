<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Product;
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
        $this->assertIsArray($this->category->getChildren());
        $this->assertSame([], $this->category->getProducts());
        $this->assertIsArray($this->category->getProducts());
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
     * @group category-set-children
     */
    public function testCategorySetChildren(): void
    {
        $this->category->setChildren([new Category(), new Category()]);
        $this->assertSame(2, count($this->category->getChildren()));
        $this->assertIsArray($this->category->getChildren());
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
        $this->assertIsArray($this->category->getChildren());
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
        $this->assertIsArray($this->category->getChildren());
    }

    /**
     * @group entity
     * @group category
     * @group category-set-products
     */
    public function testCategorySetProducts(): void
    {
        $products = [];
        for ($i = 1; $i <= 2; $i++) {
            $products[] = new Product();
        }
        $this->category->setProducts($products);
        $this->assertSame(2, count($this->category->getProducts()));
        $this->assertIsArray($this->category->getProducts());

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
        $this->assertIsArray($this->category->getProducts());
    }
}