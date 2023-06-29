<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Factory\OrderFactory;
use App\Service\Cart\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;

class CartManager
{

    private CartSessionStorage $cartSessionStorage;
    private Security $security;
    private OrderFactory $orderFactory;
    private EntityManagerInterface $entityManager;

    public function __construct(CartSessionStorage $cartSessionStorage,Security $security, OrderFactory $orderFactory, EntityManagerInterface $entityManager)
    {
        $this->cartSessionStorage = $cartSessionStorage;
        $this->security = $security;
        $this->orderFactory = $orderFactory;
        $this->entityManager = $entityManager;
    }

    public function getCurrentCart(): Order
    {
        try {
            $cart = $this->cartSessionStorage->getCart();
        } catch (NonUniqueResultException $e) {
            $cart = null;
        }

        if (!$cart) {
            $cart = $this->orderFactory->create($this->security->getUser());
            $this->cartSessionStorage->setCart($cart);
        }

        return $cart;
    }

    public function getOrderItemByProduct(Product $product): ?OrderItem
    {
        $cart = $this->getCurrentCart();

        foreach ($cart->getOrderItems() as $cartItem) {
            if ($cartItem->getProduct() === $product) {
                return $cartItem;
            }
        }

        return null;
    }

    public function saveCart(Order $cart): void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $this->cartSessionStorage->setCart($cart);
    }

    public function getCartFactory(): OrderFactory
    {
        return $this->orderFactory;
    }
}
