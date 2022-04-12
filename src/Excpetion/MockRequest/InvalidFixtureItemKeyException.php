<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion\MockRequest;

use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;

class InvalidFixtureItemKeyException extends ExtendedMockHttpClientParameterizedException
{
    /**
     * @param string[] $allowedKeys
     */
    public function __construct(array $allowedKeys, string $actualKey)
    {
        parent::__construct(
            'Invalid fixture item key',
            [
                'allowedKeys' => $allowedKeys,
                'actualKey' => $actualKey,
            ],
            500
        );
    }
}
