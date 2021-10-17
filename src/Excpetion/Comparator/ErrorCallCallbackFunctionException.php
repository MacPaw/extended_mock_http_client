<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion\Comparator;

use ExtendedMockHttpClient\Excpetion\ExtendedMockHttpClientParameterizedException;
use Throwable;

class ErrorCallCallbackFunctionException extends ExtendedMockHttpClientParameterizedException
{
    public function __construct(Throwable $parent, $value)
    {
        parent::__construct('Error to call callback function', [
            'value' => $value,
            'parent' => $parent,
        ], 500);
    }
}
