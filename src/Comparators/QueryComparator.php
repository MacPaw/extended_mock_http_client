<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

class QueryComparator extends AndComparator
{
    public function isMatch($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $query = parse_url($value, PHP_URL_QUERY);
        parse_str($query, $dataAsArray);

        return parent::isMatch($dataAsArray);
    }
}
