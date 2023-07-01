<?php

namespace App\Config\Cloudinary;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryConnexion
{
    private static ?Cloudinary $cloudinary = null;

    public static function init(): void
    {
        $configuration = Configuration::instance([
            'cloud' => [
                'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
                'api_key' => $_ENV['CLOUDINARY_API_KEY'],
                'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
            ]
        ]);

        self::$cloudinary = new Cloudinary($configuration);
    }

    public static function getCloudinary(): Cloudinary
    {
        if (self::$cloudinary === null) {
            self::init();
        }

        return self::$cloudinary;
    }
}
