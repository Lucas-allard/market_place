<?php

namespace App\Service\Order;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getOrder(int $id, string $status): ?Order
    {
        return $this->orderRepository->findOneBy([
            'id' => $id,
            'status' => $status,
        ]);
    }

    public function getOrdersByUser(?UserInterface $customer, string $status): array
    {
        return $this->orderRepository->findBy([
            'customer' => $customer,
            'orderStatus' => $status,
        ], [
            'updatedAt' => 'DESC',
        ]);
    }

}