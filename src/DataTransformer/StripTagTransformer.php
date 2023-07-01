<?php

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use function PHPUnit\Framework\assertInstanceOf;

class StripTagTransformer implements DataTransformerInterface
{
    public function transform($value): string
    {
        if (gettype($value) !== 'string') {
            return '';
        }
        return strip_tags($value);
    }

    public function reverseTransform($value): string
    {
        if (gettype($value) !== 'string') {
            return '';
        }
        return strip_tags($value);
    }
}