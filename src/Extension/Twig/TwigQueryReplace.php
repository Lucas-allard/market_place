<?php

namespace App\Extension\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigQueryReplace extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('query_replace', [$this, 'queryReplace']),
        ];
    }

    /**
     * @param $url
     * @param $parameters
     * @return string
     */
    public function queryReplace($url, $parameters): string
    {
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'] ?? '', $queryParams);

        $queryParams = array_merge($queryParams, $parameters);

        $parsedUrl['query'] = http_build_query($queryParams);

        return $parsedUrl['path'] . '?' . $parsedUrl['query'];
    }
}
