<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
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
        $this->assertInstanceOf(ArrayCollection::class, $this->product->getCategories());
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
     * @group product-add-category
     */
    public function testProductAddCategory()
    {
        $category = new Category();
        $this->product->addCategory($category);
        $this->assertInstanceOf(ArrayCollection::class, $this->product->getCategories());
        $this->assertContainsOnlyInstancesOf(Category::class, $this->product->getCategories());
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
        $this->assertCount(1, $this->product->getCategories());
    }

    /**
     * @group entity
     * @group product
     * @group product-remove-category
     */
    public function testProductRemoveCategory()
    {
        $category = new Category();
        $this->product->addCategory($category);
        $this->product->removeCategory($category);
        $this->assertCount(0, $this->product->getCategories());
    }

    /**
     * @group entity
     * @group product
     * @group product-set-seller
     */
    public function testProductSetSeller()
    {
        $seller = new Seller();
        $this->product->setSeller($seller);
        $this->assertEquals($seller, $this->product->getSeller());
    }


}
