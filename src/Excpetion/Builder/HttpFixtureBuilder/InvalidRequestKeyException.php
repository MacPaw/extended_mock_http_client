<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;

class InvalidRequestKeyException extends ExtendedMockHttpClientParameterizedException
{
    /**
     * @param string[] $allowedKeys
     */
    public function __construct(array $allowedKeys, string $actualKey)
    {
        parent::__construct(
            'Invalid request key',
            [
                'allowedKeys' => $allowedKeys,
                'actualKey' => $actualKey,
            ],
            500
        );
    }
}
