<?php

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StripTagTransformer implements DataTransformerInterface
{
    /**
     * @param $value
     * @return string
     */
    public function transform($value): string
    {
        if (!is_string($value)) {
            return '';
        }
        return strip_tags($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function reverseTransform($value): string
    {
        if (gettype($value) !== 'string') {
            return '';
        }
        return strip_tags($value);
    }
}