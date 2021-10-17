<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion;

use Throwable;

class ExtendedMockHttpClientParameterizedException extends AbstractExtendedMockHttpClientException
{
    public function __construct(string $message, array $parameters = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf("%s\n%s", $message, print_r($parameters, true)),
            $code,
            $previous
        );
    }
}
