<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\Payment;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    private Payment $payment;

    protected function setUp(): void
    {
        $this->payment = new Payment();
    }

    /**
     * @group entity
     * @group payment
     * @group payment-default
     */
    public function testPaymentDefault(): void
    {
        $this->assertEquals(0.0, $this->payment->getAmount());
        $this->assertIsFloat($this->payment->getAmount());
        $this->assertEquals('EUR', $this->payment->getCurrency());
        $this->assertIsString($this->payment->getCurrency());
        $this->assertEquals('', $this->payment->getDescription());
        $this->assertIsString($this->payment->getDescription());
        $this->assertEquals('', $this->payment->getStatus());
        $this->assertIsString($this->payment->getStatus());
        $this->assertNull($this->payment->getOrder());
        $this->assertEmpty($this->payment->getPaymentToken());
        $this->assertIsString($this->payment->getPaymentToken());
    }

    /**
     * @group entity
     * @group payment
     * @group payment-set-amount
     */
    public function testPaymentSetAmount(): void
    {
        $this->payment->setAmount(10.0);
        $this->assertSame(10.0, $this->payment->getAmount());
    }

    /**
     * @group entity
     * @group payment
     * @group payment-set-currency
     */
    public function testPaymentSetCurrency(): void
    {
        $this->payment->setCurrency('USD');
        $this->assertSame('USD', $this->payment->getCurrency());
    }

    /**
     * @group entity
     * @group payment
     * @group payment-set-description
     */
    public function testPaymentSetDescription(): void
    {
        $this->payment->setDescription('Test description');
        $this->assertSame('Test description', $this->payment->getDescription());
    }

    /**
     * @group entity
     * @group payment
     * @group payment-set-status
     */
    public function testPaymentSetStatus(): void
    {
        $this->payment->setStatus('paid');
        $this->assertSame('paid', $this->payment->getStatus());
    }

    /**
     * @group entity
     * @group payment
     * @group payment-constants
     */
    public function testPaymentConstants(): void
    {
        $this->assertSame('pending', Payment::STATUS_PENDING);
        $this->assertSame('paid', Payment::STATUS_PAID);
        $this->assertSame('failed', Payment::STATUS_FAILED);
        $this->assertSame('EUR', Payment::CURRENCY_EUR);
    }

    /**
     * @group entity
     * @group payment
     * @group payment-constants
     */
    public function testPaymentConstantsType(): void
    {
        $this->assertIsString(Payment::STATUS_PENDING);
        $this->assertIsString(Payment::STATUS_PAID);
        $this->assertIsString(Payment::STATUS_FAILED);
        $this->assertIsString(Payment::CURRENCY_EUR);
    }

    /**
     * @group entity
     * @group payment
     * @group payment-set-order
     */
    public function testPaymentSetOrder(): void
    {
        $this->payment->setOrder(new Order());
        $this->assertInstanceOf(Order::class, $this->payment->getOrder());
    }

    /**
     * @group entity
     * @group payment
     * @group payment-set-order-null
     */
    public function testSetOrderWithNull()
    {
        $payment = new Payment();
        $order = new Order();
        $payment->setOrder($order);

        $this->assertEquals($order, $payment->getOrder());

        $payment->setOrder(null);

        $this->assertEquals(null, $payment->getOrder());
    }

    /**
     * @group entity
     * @group payment
     * @group payment-set-order-existing
     */
    public function testSetOrderWithExistingOrder()
    {
        $payment = new Payment();
        $order = new Order();
        $payment->setOrder($order);

        $this->assertEquals($order, $payment->getOrder());

        $newOrder = new Order();
        $newOrder->setPayment(new Payment());
        $payment->setOrder($newOrder);

        $this->assertEquals($newOrder, $payment->getOrder());
        $this->assertEquals($payment, $order->getPayment());
    }


    /**
     * @group entity
     * @group payment
     * @group payment-set-payment-token
     */
    public function testPaymentSetPaymentToken(): void
    {
        $this->payment->setPaymentToken('test-token');
        $this->assertSame('test-token', $this->payment->getPaymentToken());
    }
}