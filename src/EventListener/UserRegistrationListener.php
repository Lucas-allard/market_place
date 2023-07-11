<?php

namespace App\EventListener;

use App\Entity\Interface\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationListener implements EventSubscriberInterface
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => 'prePersist',
        ];
    }

    /**
     * @param PrePersistEventArgs $args
     * @return void
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof UserInterface) {
            return;
        }
        $this->encodePassword($user, $user->getPassword());
    }

    /**
     * @param UserInterface $user
     * @param string $plainPassword
     * @return void
     */
    private function encodePassword(UserInterface $user, string $plainPassword): void
    {
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
    }
}