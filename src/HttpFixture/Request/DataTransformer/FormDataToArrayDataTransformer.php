<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\HttpFixture\Request\DataTransformer;

use Throwable;

class FormDataToArrayDataTransformer extends AbstractNestedDataTransformerItem
{
    public static function getName(): string
    {
        return 'formDataToArray';
    }

    public function __invoke($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            parse_str($value, $result);
        } catch (Throwable $throwable) {
            return false;
        }

        return parent::__invoke($result);
    }
}
