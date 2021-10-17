<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

class UrlComparator extends AndComparator
{
    public function isMatch($value): bool
    {
        [$url] = explode('?', $value);

        return parent::isMatch($url);
    }
}
