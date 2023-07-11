<?php

namespace App\EventListener;

use App\Entity\Customer;
use App\Service\Cart\CartSessionStorage;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManagerInterface;

#[AsDoctrineListener('security.interactive_login', priority: 1)]
class AuthenticationSuccessListener
{
    /**
     * @var CartSessionStorage
     */
    private CartSessionStorage $cartSessionStorage;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(CartSessionStorage $cartSessionStorage, EntityManagerInterface $entityManager)
    {
        $this->cartSessionStorage = $cartSessionStorage;
        $this->entityManager = $entityManager;
    }


    /**
     * @param InteractiveLoginEvent $event
     * @return void
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $customer = $event->getAuthenticationToken()->getUser();

        try {
            $panier = $this->cartSessionStorage->getCartWithoutUser();
        } catch (NonUniqueResultException $e) {
            $panier = null;
        }

        if ($customer instanceof Customer && $panier !== null) {
            $panier->setCustomer($customer);

            $this->entityManager->persist($panier);
            $this->entityManager->flush();
        }
    }
}
