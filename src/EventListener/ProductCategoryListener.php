<?php

namespace App\EventListener;

use App\Entity\Product;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductCategoryListener implements EventSubscriberInterface
{

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    public function onPostSubmit(FormEvent $event): void
    {
        $product = $event->getData();

        if (!$product instanceof Product) {
            return;
        }

        $this->updateProductCategories($product);
    }

    /**
     * @param Product $product
     * @return void
     */
    private function updateProductCategories(Product $product): void
    {
        foreach ($product->getCategories() as $category) {
            if ($category->getParent()) {
                $product->addCategory($category->getParent());
            }
        }
    }
}