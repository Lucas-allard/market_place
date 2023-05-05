<?php

namespace App\Tests\Repository;

use PHPUnit\Framework\TestCase;

class OrderRepositoryTest extends TestCase
{
    private OrderRepository $orderRepository;

    public function setUp(): void
    {
        $this->orderRepository = new OrderRepository();
    }


}