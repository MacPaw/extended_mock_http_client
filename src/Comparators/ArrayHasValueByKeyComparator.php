<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

class ArrayHasValueByKeyComparator implements ComparatorInterface
{
    private $key;
    private $value;

    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function isMatch($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        return isset($value[$this->key]) && $value[$this->key] === $this->value;
    }
}
