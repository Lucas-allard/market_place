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
    public function upload(?File $file, string $folder, string $fileName, array $transformation): string
    {
        $cloudinary = CloudinaryConnexion::getCloudinary();

        $result = $cloudinary->uploadApi()->upload($file->getPathname(), [
            'folder' => $folder,
            'public_id' => $fileName,
            'resource_type' => 'auto',
            'transformation' => $transformation,
        ]);


        return $result['secure_url'];
    }
}