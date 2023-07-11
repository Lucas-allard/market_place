<?php

namespace App\EventListener;

use App\Service\Cloudinary\CloudinaryService;
use Cloudinary\Api\Exception\ApiError;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddPictureFieldListener implements EventSubscriberInterface
{

    /**
     * @var CloudinaryService
     */
    private CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
            FormEvents::POST_SET_DATA => 'onPostSetData',
        ];
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    public function onPostSubmit(FormEvent $event): void
    {
        $product = $event->getData();
        $form = $event->getForm();

        $pictures = $form->get('pictures')->getData();


        foreach ($pictures as $picture) {
            if (null === $picture->getFile()) {
                continue;
            }

            $file = $picture->getFile();

            $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $uniqueFileName = $originalFileName . '_' . uniqid();

            try {
                $imageUrl = $this->cloudinaryService->upload($file, 'seller-' . $product->getSeller()->getId() . '/products', $uniqueFileName, ['width' => 800, 'height' => 800, 'crop' => 'fill']);
                $imageThumbnailUrl = $this->cloudinaryService->upload($file, 'seller-' . $product->getSeller()->getId() . '/products/thumbnails', $uniqueFileName, ['width' => 200, 'height' => 200, 'crop' => 'fill']);

                $picture->setPath($imageUrl);
                $picture->setThumbnail($imageThumbnailUrl);

            } catch (ApiError $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    public function onPostSetData(FormEvent $event): void
    {

    }
}