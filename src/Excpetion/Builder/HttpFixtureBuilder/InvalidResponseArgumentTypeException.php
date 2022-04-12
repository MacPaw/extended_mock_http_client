<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion\Builder\HttpFixtureBuilder;

use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;

class InvalidResponseArgumentTypeException extends ExtendedMockHttpClientParameterizedException
{
    /**
     * @param mixed $actualArgument
     */
    public function __construct(int $argumentIndex, array $allowedArgumentTypes, $actualArgument)
    {
        if (is_object($actualArgument)) {
            $actualArgumentType = get_class($actualArgument);
        } else {
            $actualArgumentType = gettype($actualArgument);
        }

        parent::__construct(
            'Invalid response argument type',
            [
                'argumentIndex' => $argumentIndex,
                'allowedArgumentTypes' => $allowedArgumentTypes,
                'actualArgumentType' => $actualArgumentType,
            ],
            500
        );
    }
}
