<?php

namespace ExtendedMockHttpClient\Comparators;

interface ComparatorInterface
{
    public function isMatch($value): bool;
}
