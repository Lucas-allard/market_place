<?php

namespace App\EventListener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugAssignerListener implements EventSubscriberInterface
{

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => 'prePersist',
        ];
    }

    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();

        if (!method_exists($entity, 'getSlug')) {
            return;
        }

        $slug = $this->slugger->slug($entity->getName())->lower();

        $entity->setSlug($slug);
    }
}