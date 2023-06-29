<?php

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class TrimTransformer implements DataTransformerInterface
{

    public function transform($value): string
    {
        return trim($value);
    }

    public function reverseTransform($value): string
    {
        return $value;
    }
}