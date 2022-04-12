<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\DataTransformer;

use Throwable;

class JsonToArrayDataTransformer extends AbstractNestedDataTransformerItem
{
    public static function getName(): string
    {
        return 'jsonToArray';
    }

    public function __invoke($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            $result = json_decode($value, true);
        } catch (Throwable $throwable) {
            return false;
        }

        return parent::__invoke($result);
    }
}
