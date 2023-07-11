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
    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @var OrderRepository
     */
    private OrderRepository $orderRepository;

    /**
     * @var Security
     */
    private Security $security;

    const CART_SESSION_NAME = 'cart_id';

    /**
     * @param RequestStack $requestStack
     * @param OrderRepository $orderRepository
     * @param Security $security
     */
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

    /**
     * @param Order $order
     * @return void
     */
    public function setCart(Order $order): void
    {
        $this->requestStack->getCurrentRequest()->getSession()->set(self::CART_SESSION_NAME, $order->getId());
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    /**
     * @return int|null
     */
    public function getCartId(): ?int
    {
        return $this->getSession()->get(self::CART_SESSION_NAME);
    }

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->requestStack->getCurrentRequest()->getSession();
    }
}