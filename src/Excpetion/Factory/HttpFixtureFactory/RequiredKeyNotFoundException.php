<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion\Factory\HttpFixtureFactory;

use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;

class RequiredKeyNotFoundException extends ExtendedMockHttpClientParameterizedException
{
    public function __construct(string $key)
    {
        parent::__construct(
            'Required key not found',
            [
                'key' => $key,
            ],
            500
        );
    }
}
