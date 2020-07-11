<?php

namespace ExtendedMockHttpClient\Excpetion;

use Exception;

class ExtendedMockHttpClientException extends Exception
{
    protected static function arrayParametersToString(array $parameters): string
    {
        $result = [];

        foreach ($parameters as $key => $parameter) {
            $result[] = sprintf("%s: %s", $key, $parameter);
        }

        return implode("\n", $result);
    }
}
