<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\DataTransformer;

class UrlToArrayDataTransformer extends AbstractNestedDataTransformerItem
{
    public static function getName(): string
    {
        return 'urlToArray';
    }

    public function __invoke($value): bool
    {
        [$url] = explode('?', $value);

        $urlAsArray = parse_url($url);

        return parent::__invoke($urlAsArray);
    }
}
