<?php

namespace App\Factory;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;

class OrderFactory
{
    /**
     *
     * @param Customer|null $user
     * @return Order
     */
    public function create(Customer $user = null): Order
    {
        $order = new Order();
        $order
            ->setOrderStatus(Order::STATUS_CART)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setCustomer($user);

        return $order;
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return OrderItem
     */
    public function createItem(Product $product, int $quantity): OrderItem
    {
        $item = new OrderItem();
        $item->setProduct($product);
        $item->setQuantity($quantity);

        return $item;
    }
}