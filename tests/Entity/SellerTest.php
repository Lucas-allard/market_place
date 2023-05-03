<?php

namespace App\Tests\Entity;

use App\Entity\Seller;
use PHPUnit\Framework\TestCase;

class SellerTest extends TestCase
{
    private Seller $seller;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->seller = new Seller();
    }

    /**
     * @group entity
     * @group seller
     * @group seller-default
     */
    public function testSellerDefault(): void
    {
        $this->assertEmpty($this->seller->getCompany());
        $this->assertIsString($this->seller->getCompany());
        $this->assertEmpty($this->seller->getSiret());
        $this->assertIsString($this->seller->getSiret());
        $this->assertEmpty($this->seller->getVat());
        $this->assertIsString($this->seller->getVat());
        $this->assertEquals(0.0, $this->seller->getSellerRating());
        $this->assertIsFloat($this->seller->getSellerRating());
        $this->assertEmpty($this->seller->getProducts());
        $this->assertIsArray($this->seller->getProducts());
        $this->assertEmpty($this->seller->getSales());
        $this->assertIsArray($this->seller->getSales());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-set-company
     */
    public function testSellerSetCompany(): void
    {
        $this->seller->setCompany('Company');
        $this->assertSame('Company', $this->seller->getCompany());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-set-siret
     */
    public function testSellerSetSiret(): void
    {
        $this->seller->setSiret('Siret');
        $this->assertSame('Siret', $this->seller->getSiret());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-set-vat
     */
    public function testSellerSetVat(): void
    {
        $this->seller->setVat('Vat');
        $this->assertSame('Vat', $this->seller->getVat());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-set-seller-rating
     */
    public function testSellerSetSellerRating(): void
    {
        $this->seller->setSellerRating(1.0);
        $this->assertSame(1.0, $this->seller->getSellerRating());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-set-products
     */
    public function testSellerSetProducts(): void
    {
        $this->seller->setProducts([
            ['produit 1' => 1,],
            ['produit 2' => 2,],
            ['produit 3' => 3,],
        ]);
        $this->assertSame([
            ['produit 1' => 1,],
            ['produit 2' => 2,],
            ['produit 3' => 3,],
        ], $this->seller->getProducts());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-add-product
     */
    public function testSellerAddProduct(): void
    {
        $this->seller->addProduct(['produit 1' => 1,]);
        $this->assertSame([
            ['produit 1' => 1,],
        ], $this->seller->getProducts());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-add-products-twice
     */
    public function testCannotAddSameProductTwice(): void
    {
        $this->seller->addProduct(['produit 1' => 1,]);
        $this->seller->addProduct(['produit 1' => 1,]);
        $this->assertSame([
            ['produit 1' => 1,],
        ], $this->seller->getProducts());
        $this->assertCount(1, $this->seller->getProducts());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-set-sales
     */
    public function testSellerSetSales(): void
    {
        $this->seller->setSales([
            ['vente 1' => 1,],
            ['vente 2' => 2,],
            ['vente 3' => 3,],
        ]);
        $this->assertSame([
            ['vente 1' => 1,],
            ['vente 2' => 2,],
            ['vente 3' => 3,],
        ], $this->seller->getSales());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-add-sale
     */
    public function testSellerAddSale(): void
    {
        $this->seller->addSale(['vente 1' => 1,]);
        $this->assertSame([
            ['vente 1' => 1,],
        ], $this->seller->getSales());
    }

    /**
     * @group entity
     * @group seller
     * @group seller-add-sales-twice
     */
    public function testCannotAddSameSaleTwice(): void
    {
        $this->seller->addSale(['vente 1' => 1,]);
        $this->seller->addSale(['vente 1' => 1,]);
        $this->assertSame([
            ['vente 1' => 1,],
        ], $this->seller->getSales());
        $this->assertCount(1, $this->seller->getSales());
    }
}