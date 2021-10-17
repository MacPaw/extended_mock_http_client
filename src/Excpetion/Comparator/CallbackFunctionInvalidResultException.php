<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion\Comparator;

use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;

class CallbackFunctionInvalidResultException extends ExtendedMockHttpClientParameterizedException
{
    public function __construct($result)
    {
        parent::__construct(
            'Callback function invalid result',
            [
                'expected' => 'bool',
                'actual' => gettype($result),
            ],
            500
        );
    }
}
