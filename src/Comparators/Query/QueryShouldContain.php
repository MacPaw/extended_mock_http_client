<?php

namespace ExtendedMockHttpClient\Comparators\Query;

use ExtendedMockHttpClient\Comparators\ComparatorInterface;

class QueryShouldContain implements ComparatorInterface
{
    private $fieldName;
    private $value;

    public function __construct(string $fieldName, string $value)
    {
        $this->fieldName = $fieldName;
        $this->value = $value;
    }

    public function isMatch($url): bool
    {
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $queryArray);

        return isset($queryArray[$this->fieldName]) && $queryArray[$this->fieldName] === $this->value;
    }
}
