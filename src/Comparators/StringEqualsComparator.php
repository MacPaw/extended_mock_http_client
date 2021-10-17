<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

class StringEqualsComparator implements ComparatorInterface
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function isMatch($value): bool
    {
        return $this->value === $value;
    }
}
