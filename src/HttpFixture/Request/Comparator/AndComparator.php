<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\Comparator;

class AndComparator extends AbstractNestedComparatorItem
{
    public static function getName(): string
    {
        return 'and';
    }
}
