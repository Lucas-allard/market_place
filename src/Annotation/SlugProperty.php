<?php

namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class SlugProperty
{
    /**
     * @var string
     */
    public string $property;
}