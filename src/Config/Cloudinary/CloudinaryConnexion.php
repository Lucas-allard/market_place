<?php

namespace App\Config\Cloudinary;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryConnexion
{
    /**
     * @var Cloudinary|null
     */
    private static ?Cloudinary $cloudinary = null;

    /**
     * @return void
     */
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

    /**
     * @return Cloudinary
     */
    public static function getCloudinary(): Cloudinary
    {
        if (self::$cloudinary === null) {
            self::init();
        }

        return self::$cloudinary;
    }
}
