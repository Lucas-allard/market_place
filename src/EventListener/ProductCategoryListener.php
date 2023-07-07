<?php

namespace App\EventListener;

use App\Entity\Product;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductCategoryListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => 'prePersist',
            Events::preUpdate => 'preUpdate',
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $product = $args->getObject();

        dd($product);

        if (!$product instanceof Product) {
            return;
        }

        $this->updateProductCategories($product);
    }

    public function preUpdate(PrePersistEventArgs $args): void
    {
        $product = $args->getObject();

        if (!$product instanceof Product) {
            return;
        }

        $this->updateProductCategories($product);
    }

    private function updateProductCategories(Product $product): void
    {
        foreach ($product->getCategories() as $category) {
            if ($category->getParent()) {
                $product->addCategory($category->getParent());
            }
        }
    }
}