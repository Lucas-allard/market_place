<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        $this->product = new Product();
    }

    /**
     * @group entity
     * @group product
     * @group product-default
     */
    public function testProductDefault()
    {
        $this->assertEmpty($this->product->getName());
        $this->assertIsString($this->product->getName());
        $this->assertEmpty($this->product->getDescription());
        $this->assertIsString($this->product->getDescription());
        $this->assertEquals(0.0, $this->product->getPrice());
        $this->assertIsFloat($this->product->getPrice());
        $this->assertEquals(0, $this->product->getQuantity());
        $this->assertIsInt($this->product->getQuantity());
        $this->assertEmpty($this->product->getCategories());
        $this->assertIsArray($this->product->getCategories());
        $this->assertNull($this->product->getSeller());
    }

    /**
     * @group entity
     * @group product
     * @group product-set-name
     */
    public function testProductSetName()
    {
        $this->product->setName('Product name');
        $this->assertEquals('Product name', $this->product->getName());
    }

    /**
     * @group entity
     * @group product
     * @group product-set-description
     */
    public function testProductSetDescription()
    {
        $this->product->setDescription('Product description');
        $this->assertEquals('Product description', $this->product->getDescription());
    }

    /**
     * @group entity
     * @group product
     * @group product-set-price
     */
    public function testProductSetPrice()
    {
        $this->product->setPrice(10.0);
        $this->assertEquals(10.0, $this->product->getPrice());
    }

    /**
     * @group entity
     * @group product
     * @group product-set-quantity
     */
    public function testProductSetQuantity()
    {
        $this->product->setQuantity(10);
        $this->assertEquals(10, $this->product->getQuantity());
    }

    /**
     * @group entity
     * @group product
     * @group product-add-quantity
     */
    public function testProductAddQuantity()
    {
        $this->product->setQuantity(10);
        $this->product->addQuantity(5);
        $this->assertEquals(15, $this->product->getQuantity());
    }

    /**
     * @group entity
     * @group product
     * @group product-set-categories
     */
    public function testProductSetCategories()
    {
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = new Category();
        }
        $this->product->setCategories($categories);
        $this->assertEquals($categories, $this->product->getCategories());
    }

    /**
     * @group entity
     * @group product
     * @group product-add-category
     */
    public function testProductAddCategory()
    {
        $category = new Category();
        $this->product->addCategory($category);
        $this->assertEquals([$category], $this->product->getCategories());
    }

    /**
     * @group entity
     * @group product
     * @group product-add-category-twice
     */
    public function testCannotAddCategoryTwice()
    {
        $category = new Category();
        $this->product->addCategory($category);
        $this->product->addCategory($category);
        $this->assertEquals([$category], $this->product->getCategories());
    }

}
