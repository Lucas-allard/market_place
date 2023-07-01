<?php

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UcFirstTransformer implements DataTransformerInterface
{

    /**
     * @param mixed $value
     * @return mixed|null
     */
    public function transform(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function reverseTransform(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        return ucfirst($value);
    }
}