<?php

namespace ExtendedMockHttpClient\Comparators\Url;

use ExtendedMockHttpClient\Comparators\StringEquals as BaseStringEquals;

class StringEquals extends BaseStringEquals
{
    public function __construct(string $url)
    {
        [$url] = explode('?', $url);

        parent::__construct($url);
    }

    public function isMatch($url): bool
    {
        [$url] = explode('?', $url);

        return parent::isMatch($url);
    }
}
