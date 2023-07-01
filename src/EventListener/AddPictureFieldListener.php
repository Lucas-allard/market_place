<?php

namespace App\EventListener;

use App\Entity\Picture;
use App\Service\Cloudinary\CloudinaryService;
use Cloudinary\Api\Exception\ApiError;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddPictureFieldListener implements EventSubscriberInterface
{

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
        ];
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $product = $event->getData();
        $form = $event->getForm();
        $picturesData = $form->get('pictures')->getData();

        foreach ($picturesData as $pictureData) {
            $file = $pictureData['path'];
            $alt = $pictureData['alt'];

            try {
                $imageUrl = $this->cloudinaryService->upload($file);

                $picture = new Picture();
                $picture->setPath($imageUrl);
                $picture->setAlt($alt);

                $product->addPicture($picture);

            } catch (ApiError $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }
    }
}