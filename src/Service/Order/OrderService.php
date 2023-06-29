<?php

namespace App\Service\Order;

use App\Entity\Order;
use App\Repository\OrderRepository;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getCompletedOrder(int $id): Order
    {
        return $this->orderRepository->findOneBy([
            'id' => $id,
            'status' => Order::STATUS_COMPLETED,
        ]);
    }

}