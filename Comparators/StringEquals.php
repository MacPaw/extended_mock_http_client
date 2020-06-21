<?php

namespace ExtendedMockHttpClient\Comparators;

class StringEquals implements ComparatorInterface
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
