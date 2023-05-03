<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use PHPUnit\Framework\TestCase;
use TypeError;

class OrderItemTest extends TestCase
{
    private OrderItem $orderItem;

    protected function setUp(): void
    {
        $this->orderItem = new OrderItem();
    }

    /**
     * @group entity
     * @group order-item
     * @group order-item-default
     */
    public function testOrderDefault(): void
    {
        $this->assertIsInt($this->orderItem->getQuantity());
        $this->assertEquals(0, $this->orderItem->getQuantity());
        $this->assertIsFloat($this->orderItem->getPrice());
        $this->assertEquals(0.0, $this->orderItem->getPrice());
        $this->assertNull($this->orderItem->getProduct());
        $this->assertNull($this->orderItem->getOrder());
    }

    /**
     * @group entity
     * @group order-item
     * @group order-item-set-quantity
     */

    public function testOrderSetQuantity(): void
    {
        $this->orderItem->setQuantity(8);
        $this->assertIsInt($this->orderItem->getQuantity());
        $this->assertEquals(8, $this->orderItem->getQuantity());
    }

    public function testAddQuantity(): void
    {
        $this->orderItem->setQuantity(8);
        $this->orderItem->addQuantity(2);
        $this->assertIsInt($this->orderItem->getQuantity());
        $this->assertEquals(10, $this->orderItem->getQuantity());
    }

    /**
     * @group entity
     * @group order-item
     * @group order-item-set-price
     */
    public function testOrderSetPrice(): void
    {
        $this->orderItem->setPrice(8.5);
        $this->assertIsFloat($this->orderItem->getPrice());
        $this->assertEquals(8.5, $this->orderItem->getPrice());
    }

    /**
     * @group entity
     * @group order-item
     * @group order-item-set-product
     */
    public function testOrderSetProduct(): void
    {
        $this->orderItem->setProduct('product');
        $this->assertEquals('product', $this->orderItem->getProduct());
    }

    /**
     * @group entity
     * @group order-item
     * @group order-item-set-order
     */
    public function testOrderSetOrder(): void
    {
        $order = new Order();
        $this->orderItem->setOrder($order);
        $this->assertEquals($order, $this->orderItem->getOrder());
        $this->assertInstanceOf(Order::class, $this->orderItem->getOrder());
    }

    /**
     * @group entity
     * @group order-item
     * @group order-item-set-order
     */
    public function testOrderSetOrderNull(): void
    {
        $this->orderItem->setOrder(null);
        $this->assertNull($this->orderItem->getOrder());
    }


    /**
     * @group entity
     * @group order-item
     * @group order-item-set-product
     */
    public function testOrderSetProductNull(): void
    {
        $this->orderItem->setProduct(null);
        $this->assertNull($this->orderItem->getProduct());
    }
}