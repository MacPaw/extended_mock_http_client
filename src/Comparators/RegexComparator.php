<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

class RegexComparator implements ComparatorInterface
{
    private $regex;

    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }

    public function isMatch($value): bool
    {
        return is_string($value) && preg_match($this->regex, $value) === 1;
    }
}
