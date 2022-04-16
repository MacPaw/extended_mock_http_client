<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\Comparator;

class OrComparator extends AbstractNestedComparatorItem
{
    public static function getName(): string
    {
        return 'or';
    }

    public function __invoke($value): bool
    {
        foreach ($this->items as $item) {
            if ($item->__invoke($value)) {
                return true;
            }
        }

        return false;
    }
}
