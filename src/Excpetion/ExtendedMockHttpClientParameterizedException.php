<?php

declare(strict_types=1);

namespace ExtendedMockHttpClient\Excpetion;

use Throwable;

class ExtendedMockHttpClientParameterizedException extends AbstractExtendedMockHttpClientException
{
    public function __construct(string $message, array $parameters = [], int $code = 0, Throwable $previous = null)
    {
        $parametersAsString = '';
        foreach ($parameters as $key => $value) {
            // @codingStandardsIgnoreStart
            $valueAsString = print_r($value, true);
            // @codingStandardsIgnoreEnd

            $parametersAsString .= sprintf("    %s: %s\n", $key, $valueAsString);
        }

        parent::__construct(
            sprintf("%s\n%s", $message, $parametersAsString),
            $code,
            $previous
        );
    }
}
