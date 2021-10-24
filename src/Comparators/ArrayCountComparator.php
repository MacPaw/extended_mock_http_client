<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

class ArrayCountComparator implements ComparatorInterface
{
    private $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function isMatch($value): bool
    {
        if (!is_array($value)) {
            return false;
        }


        return count($value) === $this->count;
    }
}
