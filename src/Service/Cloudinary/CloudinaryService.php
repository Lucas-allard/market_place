<?php

namespace App\Service\Cloudinary;

use App\Config\Cloudinary\CloudinaryConnexion;
use Cloudinary\Api\Exception\ApiError;
use Symfony\Component\HttpFoundation\File\File;

class CloudinaryService
{

    /**
     * @throws ApiError
     */
    public function upload(File $file): string
    {
        $cloudinary = CloudinaryConnexion::getCloudinary();

        $filePath = $file->getPathname();

        $result = $cloudinary->uploadApi()->upload($filePath);

        return $result['secure_url'];
    }

    /**
     * @throws ApiError
     */
    public function uploadMultiple(array $uploadedFiles): array
    {
        $cloudinary = CloudinaryConnexion::getCloudinary();
        $uploadedUrls = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $filePath = $uploadedFile->getPathname();
            $result = $cloudinary->uploadApi()->upload($filePath);
            $uploadedUrls[] = $result['secure_url'];
        }

        return $uploadedUrls;
    }
}