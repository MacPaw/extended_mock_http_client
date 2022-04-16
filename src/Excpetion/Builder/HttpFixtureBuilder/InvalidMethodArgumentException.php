<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;

class InvalidMethodArgumentException extends ExtendedMockHttpClientParameterizedException
{
    /**
     * @param string[] $expectedTypes
     */
    public function __construct(string $method, array $expectedTypes, $actualArgument)
    {
        if (is_object($actualArgument)) {
            $actualArgumentType = get_class($actualArgument);
        } else {
            $actualArgumentType = gettype($actualArgument);
        }

        parent::__construct(
            'Invalid method argument',
            [
                'method' => $method,
                'expectedTypes' => $expectedTypes,
                'actualArgumentType' => $actualArgumentType,
            ],
            500
        );
    }
}
