<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemSeller;
use App\Entity\Product;
use App\Entity\Seller;
use App\Factory\OrderFactory;
use App\Service\Cart\CartSessionStorage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;

class CartManager
{

    /**
     * @var CartSessionStorage
     */
    private CartSessionStorage $cartSessionStorage;
    /**
     * @var Security
     */
    private Security $security;
    /**
     * @var OrderFactory
     */
    private OrderFactory $orderFactory;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param CartSessionStorage $cartSessionStorage
     * @param Security $security
     * @param OrderFactory $orderFactory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(CartSessionStorage $cartSessionStorage, Security $security, OrderFactory $orderFactory, EntityManagerInterface $entityManager)
    {
        $this->cartSessionStorage = $cartSessionStorage;
        $this->security = $security;
        $this->orderFactory = $orderFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @return Order
     */
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

    /**
     * @param Product $product
     * @return OrderItem|null
     */
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

    /**
     * @param Seller $seller
     * @return OrderItemSeller|null
     */
    public function getOrderItemSellerBySeller(Seller $seller): ?OrderItemSeller
    {
        $cart = $this->getCurrentCart();

        foreach ($cart->getOrderItemSellers() as $cartItemSeller) {
            if ($cartItemSeller->getSeller() === $seller) {
                return $cartItemSeller;
            }
        }

        return null;
    }

    /**
     * @param Order $cart
     * @return void
     */
    public function saveCart(Order $cart): void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $this->cartSessionStorage->setCart($cart);
    }

    /**
     * @return OrderFactory
     */
    public function getCartFactory(): OrderFactory
    {
        return $this->orderFactory;
    }
}
