<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion\Factory\HttpFixtureFactory;

use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;

class NotFoundKeyInAllowedNestedKeysException extends ExtendedMockHttpClientParameterizedException
{
    public function __construct(string $key)
    {
        parent::__construct(
            'Not found key in allowed nested keys',
            [
                'key' => $key,
            ],
            500
        );
    }
}
