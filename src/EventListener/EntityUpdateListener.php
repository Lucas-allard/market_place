<?php

namespace App\EventListener;

use DateTime;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityUpdateListener implements EventSubscriberInterface
{

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::onFlush => 'onFlush',
        ];
    }

    /**
     * @param OnFlushEventArgs $args
     * @return void
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($this->isRootEntity($entity,$unitOfWork) && method_exists($entity, 'setUpdatedAt')) {
                $entity->setUpdatedAt(new DateTime());
                $entityManager->persist($entity);
                $unitOfWork->recomputeSingleEntityChangeSet($entityManager->getClassMetadata(get_class($entity)), $entity);
            }
        }
    }

    /**
     * @param object $entity
     * @param UnitOfWork $unitOfWork
     * @return bool
     */
    private function isRootEntity(object $entity, UnitOfWork $unitOfWork): bool
    {
        $entityChangeSet = $unitOfWork->getEntityChangeSet($entity);

        return count($entityChangeSet) > 0;
    }

}