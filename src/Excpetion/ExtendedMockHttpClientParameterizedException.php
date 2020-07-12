<?php

namespace ExtendedMockHttpClient\Excpetion;

use Exception;
use Throwable;

class ExtendedMockHttpClientParameterizedException extends Exception
{
    private $parameters;

    public function __construct(string $message, array $parameters = [], int $code = 0, Throwable $previous = null)
    {
        $this->parameters = $parameters;

        parent::__construct($message, $code, $previous);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
