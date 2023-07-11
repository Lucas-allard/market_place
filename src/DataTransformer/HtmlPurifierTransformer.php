<?php

namespace App\DataTransformer;

use HTMLPurifier;
use HTMLPurifier_Config;
use Symfony\Component\Form\DataTransformerInterface;

class HtmlPurifierTransformer implements DataTransformerInterface
{
    /**
     * @var HTMLPurifier
     */
    private HTMLPurifier $purifier;


    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p, ul, li, ol, strong, em, u, s, a[href|title], img[src|alt], h3, h4, h5, h6, blockquote, pre, code, br, hr');
        $this->purifier = new HTMLPurifier($config);
    }


    /**
     * @param mixed $value
     * @return mixed
     */
    public function transform(mixed $value): mixed
    {
        return $value;
    }


    /**
     * @param mixed $value
     * @return mixed|string|null
     */
    public function reverseTransform(mixed $value): mixed
    {
        return $this->purifier->purify($value);
    }
}