<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;

class InvalidResponseMethodArgumentCountException extends ExtendedMockHttpClientParameterizedException
{
    public function __construct(int $minArgumentCount, int $maxArgumentCount, int $actualArgumentCount)
    {
        parent::__construct(
            'Invalid response method argument count',
            [
                'minArgumentCount' => $minArgumentCount,
                'maxArgumentCount' => $maxArgumentCount,
                'actualArgumentCount' => $actualArgumentCount,
            ],
            500
        );
    }
}
