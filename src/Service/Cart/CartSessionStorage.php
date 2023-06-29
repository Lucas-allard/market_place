<?php

namespace App\Service\Cart;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CartSessionStorage
{
    private RequestStack $requestStack;

    private OrderRepository $orderRepository;

    private Security $security;

    const CART_SESSION_NAME = 'cart_id';

    public function __construct(RequestStack $requestStack, OrderRepository $orderRepository, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
        $this->security = $security;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getCart(): ?Order
    {
            return $this->orderRepository->findCart($this->getCartId(), $this->getUser());
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getCartWithoutUser(): ?Order
    {
        return $this->orderRepository->findCart($this->getCartId(), null);
    }

    public function setCart(Order $order): void
    {
        $this->requestStack->getCurrentRequest()->getSession()->set(self::CART_SESSION_NAME, $order->getId());
    }

    public function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    public function getCartId(): ?int
    {
        return $this->getSession()->get(self::CART_SESSION_NAME);
    }

    public function getSession(): SessionInterface
    {
        return $this->requestStack->getCurrentRequest()->getSession();
    }
}