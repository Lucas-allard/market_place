<?php

namespace App\EventListener;

use App\Annotation\SlugProperty;
use DateTime;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugAssignerListener implements EventSubscriberInterface
{

    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * @var Reader
     */
    private Reader $reader;

    public function __construct(SluggerInterface $slugger, Reader $reader)
    {
        $this->slugger = $slugger;
        $this->reader = $reader;
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

    /**
     * @param PrePersistEventArgs $event
     * @return void
     * @throws \ReflectionException
     */
    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();

        if (!method_exists($entity, 'getSlug')) {
            return;
        }

        $annotation = $this->getSlugAnnotation($entity);

        if (!$annotation) {
            return;
        }

        $property = $annotation->property;

        if (!property_exists($entity, $property)) {
            return;
        }

        $propertyValue = $this->getPropertyValue($entity, $property);

        if ($propertyValue instanceof DateTime) {
            // change property value to stringable
            $propertyValue = $propertyValue->format('Y-m-d H:i:s');

        }
        $slug = $this->slugger->slug((string)$propertyValue)->lower();

        $entity->setSlug($slug);
    }

    /**
     * @param object $entity
     * @return SlugProperty|null
     */
    private function getSlugAnnotation(object $entity): ?SlugProperty
    {
        $reflectionClass = new \ReflectionClass($entity);

        $annotationClass = $this->reader->getClassAnnotation($reflectionClass, SlugProperty::class);

        if (!$annotationClass) {
            return null;
        }

        return $annotationClass;
    }

    /**
     * @throws \ReflectionException
     */
    private function getPropertyValue(object $entity, string $property)
    {
        $reflectionClass = new \ReflectionClass($entity);

        $reflectionProperty = $reflectionClass->getProperty($property);

        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($entity);
    }
}