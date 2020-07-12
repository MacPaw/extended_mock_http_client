<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

interface ComparatorInterface
{
    public function isMatch($value): bool;
}
