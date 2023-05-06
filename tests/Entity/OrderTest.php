<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Payment;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Stripe\Collection;

class OrderTest extends TestCase
{
    private Order $order;

    protected function setUp(): void
    {
        $this->order = new Order();
    }

    /**
     * @group entity
     * @group order
     * @group order-default
     */
    public function testOrderDefault(): void
    {
        $this->assertNull($this->order->getOrderDate());
        $this->assertNull($this->order->getDeliveryDate());
        $this->assertIsString($this->order->getOrderStatus());
        $this->assertEmpty($this->order->getOrderStatus());
        $this->assertIsFloat($this->order->getTotalAmount());
        $this->assertEquals(0.0, $this->order->getTotalAmount());
        $this->assertNull($this->order->getCustomer());
        $this->assertContainsOnlyInstancesOf(OrderItem::class, $this->order->getOrderItems());
        $this->assertEmpty($this->order->getOrderItems());
        $this->assertNull($this->order->getPayment());
    }

    /**
     * @group entity
     * @group order
     * @group order-set-order-date
     */
    public function testOrderSetOrderDate(): void
    {
        $date = new DateTime();
        $this->order->setOrderDate($date);
        $this->assertSame($date, $this->order->getOrderDate());
    }

    /**
     * @group entity
     * @group order
     * @group order-set-delivery-date
     */
    public function testOrderSetDeliveryDate(): void
    {
        $date = new DateTime();
        $this->order->setDeliveryDate($date);
        $this->assertSame($date, $this->order->getDeliveryDate());
    }

    /**
     * @group entity
     * @group order
     * @group order-set-order-status
     */
    public function testOrderSetOrderStatus(): void
    {
        $this->order->setOrderStatus(Order::STATUS_PENDING);
        $this->assertSame(Order::STATUS_PENDING, $this->order->getOrderStatus());
        $this->order->setOrderStatus(Order::STATUS_COMPLETED);
        $this->assertSame(Order::STATUS_COMPLETED, $this->order->getOrderStatus());
    }

    /**
     * @group entity
     * @group order
     * @group order-set-total-amount
     */
    public function testOrderSetTotalAmount(): void
    {
        $this->order->setTotalAmount(10.0);
        $this->assertSame(10.0, $this->order->getTotalAmount());
    }

    /**
     * @group entity
     * @group order
     * @group order-set-customer
     */
    public function testOrderSetCustomer(): void
    {
        $customer = $this->createMock('App\Entity\Customer');
        $this->order->setCustomer($customer);
        $this->assertSame($customer, $this->order->getCustomer());
        $this->assertInstanceOf('App\Entity\Customer', $this->order->getCustomer());
    }

    /**
     * @group entity
     * @group order
     * @group order-set-order-items
     */
    public function testOrderSetOrderItems(): void
    {
        $orderItems = new ArrayCollection();
        $orderItem = new OrderItem();
        $orderItems->add($orderItem);
        $this->order->setOrderItems($orderItems);
        $this->assertContains($orderItem, $this->order->getOrderItems());
    }

    /**
     * @group entity
     * @group order
     * @group order-add-order-item
     */
    public function testOrderAddOrderItem(): void
    {
        $orderItem = new OrderItem();
        $this->order->addOrderItem($orderItem);
        $this->assertContains($orderItem, $this->order->getOrderItems());
        $this->assertInstanceOf('App\Entity\OrderItem', $this->order->getOrderItems()[0]);
    }

    /**
     * @group entity
     * @group order
     * @group order-add-order-item-twice
     */
    public function testCannotAddOrderItemTwice(): void
    {
        $orderItem = new OrderItem();
        $this->order->addOrderItem($orderItem);
        $this->order->addOrderItem($orderItem);
        $this->assertCount(1, $this->order->getOrderItems());
    }

    /**
     * @group entity
     * @group order
     * @group order-set-payment
     */
    public function testOrderSetPayment(): void
    {
        $payment = new Payment();
        $this->order->setPayment($payment);
        $this->assertSame($payment, $this->order->getPayment());
        $this->assertInstanceOf('App\Entity\Payment', $this->order->getPayment());
    }
}