<?php

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class TrimTransformer implements DataTransformerInterface
{

    /**
     * @param $value
     * @return string
     */
    public function transform($value): string
    {
        return trim($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function reverseTransform($value): string
    {
        return $value;
    }
}