<?php

namespace App\Factory;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemSeller;
use App\Entity\Product;

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
        $order->setCustomer($user);

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

    /**
     * @param Product $product
     * @return OrderItemSeller
     */
    public function createItemSeller(Product $product): OrderItemSeller
    {
        $item = new OrderItemSeller();
        $item->setSeller($product->getSeller());

        return $item;
    }
}