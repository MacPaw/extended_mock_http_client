<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\DataTransformer;

class QueryToArrayDataTransformer extends AbstractNestedDataTransformerItem
{
    public static function getName(): string
    {
        return 'queryToArray';
    }

    public function __invoke($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        parse_str($value, $queryAsArray);

        return parent::__invoke($queryAsArray);
    }
}
