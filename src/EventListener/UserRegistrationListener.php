<?php

namespace App\EventListener;

use App\Entity\Interface\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
class UserRegistrationListener
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof UserInterface) {
            return;
        }
        $this->encodePassword($user, $user->getPassword());
    }
    private function encodePassword(UserInterface $user, string $plainPassword): void
    {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
    }
}