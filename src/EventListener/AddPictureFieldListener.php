<?php

namespace App\EventListener;

use App\Entity\AbstractEntity;
use App\Entity\Picture;
use App\Service\Cloudinary\CloudinaryService;
use Cloudinary\Api\Exception\ApiError;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class AddPictureFieldListener implements EventSubscriberInterface
{
    /**
     * @var CloudinaryService
     */
    private CloudinaryService $cloudinaryService;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param CloudinaryService $cloudinaryService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(CloudinaryService $cloudinaryService, EntityManagerInterface $entityManager)
    {
        $this->cloudinaryService = $cloudinaryService;
        $this->entityManager = $entityManager;
    }

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
        $entity = $event->getData();
        $form = $event->getForm();

        if (!$entity instanceof AbstractEntity) {
            return;
        }

        $folderNames = $this->getFolderNames($entity);

        $this->uploadPictures($form, $folderNames);
    }

    /**
     * @param FormInterface $form
     * @param array $folderNames
     * @return void
     */
    private function uploadPictures(FormInterface $form, array $folderNames): void
    {

        $picture = $form->has('picture') ? $form->get('picture')->getData() : null;
        $pictures = $form->has('pictures') ? $form->get('pictures')->getData() : null;

        if ($picture !== null) {
            $this->uploadPicture($picture, $folderNames, $form);
        }

        if ($pictures !== null) {
            foreach ($pictures as $picture) {
                $this->uploadPicture($picture, $folderNames, $form);
            }
        }
    }

    /**
     * @param Picture $picture
     * @param array $folderNames
     * @param FormInterface $form
     * @return void
     */
    private function uploadPicture(Picture $picture, array $folderNames, FormInterface $form): void
    {
        if ($picture->getFile() === null) {
            return;
        }

        $file = $picture->getFile();
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $uniqueFileName = $originalFileName . '_' . uniqid();

        try {
            $imageUrl = $this->cloudinaryService->upload(
                $file,
                $folderNames[0],
                $uniqueFileName,
                [
                    'width' => 800,
                    'height' => 800,
                    'crop' => 'fill'
                ]
            );

            // the height must be fixed with the width

            $imageThumbnailUrl = $this->cloudinaryService->upload(
                $file,
                $folderNames[1],
                $uniqueFileName,
                [
                    'width' => 70,
                    'height' => 53,
                    'scale' => 'scale',
                ]
            );

            $picture->setPath($imageUrl);
            $picture->setThumbnail($imageThumbnailUrl);

            $this->entityManager->persist($picture);
            $this->entityManager->flush();
        } catch (ApiError $e) {
            $form->addError(new FormError($e->getMessage()));
        }
    }

    /**
     * @param AbstractEntity $entity
     * @return string[]
     */
    private function getFolderNames(AbstractEntity $entity): array
    {
        $entityName = strtolower((new ReflectionClass($entity))->getShortName());

        return [
            $entityName . 's',
            $entityName . 's/thumbnails',
        ];
    }
}
