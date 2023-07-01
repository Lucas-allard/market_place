<?php

namespace App\DataTransformer;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlPurifierTransformer implements \Symfony\Component\Form\DataTransformerInterface
{
    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p, ul, li, ol, strong, em, u, s, a[href|title], img[src|alt], h3, h4, h5, h6, blockquote, pre, code, br, hr');
        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * @inheritDoc
     */
    public function transform(mixed $value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform(mixed $value)
    {
        return $this->purifier->purify($value);
    }
}