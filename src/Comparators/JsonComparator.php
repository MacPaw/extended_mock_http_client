<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Comparators;

class JsonComparator extends AndComparator
{
    public function isMatch($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $data = json_decode($value, true);
        if ($data === null) {
            return false;
        }

        return parent::isMatch($data);
    }
}
